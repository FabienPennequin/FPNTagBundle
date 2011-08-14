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
}
