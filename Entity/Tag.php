<?php

/*
 * This file is part of the FPNTagBundle package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FPN\TagBundle\Entity;

use DoctrineExtensions\Taggable\Entity\Tag as BaseTag;

class Tag extends BaseTag
{
    protected $slug;

    /**
     * Returns tag slug
     * 
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets tag slug
     * 
     * @return string
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}
