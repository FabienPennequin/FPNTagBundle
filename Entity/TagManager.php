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

class TagManager extends BaseTagManager
{
    /**
     * @see DoctrineExtensions\Taggable\TagManager::createTag()
     */
    protected function createTag($name)
    {
        $tag = parent::createTag($name);
        $tag->setSlug($this->generateSlug($name));

        return $tag;
    }

    protected function generateSlug($name)
    {
        $slug = mb_convert_case($name, MB_CASE_LOWER, mb_detect_encoding($name));
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace('--', '-', $slug);

        return $slug;
    }
}
