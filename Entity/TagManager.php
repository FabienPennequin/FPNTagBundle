<?php

/*
 * This file is part of the FPNTagBundle package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FPN\TagBundle\Entity;

use DoctrineExtensions\Taggable\TagManager as BaseTagManager;
use Doctrine\ORM\EntityManager;
use FPN\TagBundle\Util\SlugifierInterface;

class TagManager extends BaseTagManager
{
    protected $slugifier;

    /**
     * @see DoctrineExtensions\Taggable\TagManager::__construct()
     */
    public function __construct(EntityManager $em, $tagClass = null, $taggingClass = null, SlugifierInterface $slugifier)
    {
        parent::__construct($em, $tagClass, $taggingClass);
        $this->slugifier = $slugifier;
    }

    /**
     * @see DoctrineExtensions\Taggable\TagManager::createTag()
     */
    protected function createTag($name)
    {
        $tag = parent::createTag($name);
        $tag->setSlug($this->slugifier->slugify($name));

        return $tag;
    }

    /**
     * @see DoctrineExtensions\Taggable\TagManager::loadOrCreateTags()
     */
    public function loadOrCreateTags(array $names)
    {
        if (empty($names)) {
            return array();
        }

        $names     = array_unique($names);
        $slugifier = $this->slugifier;
        $slugify   = function($name) use ($slugifier) {
            return $slugifier->slugify($name);
        };
        $slugs     = array_map($slugify, $names);
        $combine   = array_combine($slugs, $names);
        $builder   = $this->em->createQueryBuilder();

        $tags = $builder
            ->select('t')
            ->from($this->tagClass, 't')

            ->where($builder->expr()->in('t.slug', $slugs))

            ->getQuery()
            ->getResult()
        ;

        $loadedNames = array();
        foreach ($tags as $tag) {
            $loadedNames[] = $tag->getName();
        }

        $missingNames = array_udiff($slugs, $loadedNames, 'strcasecmp');
        if (sizeof($missingNames)) {
            foreach ($missingNames as $slug) {
                $tag = $this->createTag($combine[$slug]);
                $this->em->persist($tag);

                $tags[] = $tag;
            }

            $this->em->flush();
        }

        return $tags;
    }
}
