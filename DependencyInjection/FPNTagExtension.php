<?php

/*
 * This file is part of the FPNTagBundle package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FPN\TagBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FPNTagExtension extends Extension
{
    /**
     * Loads the extension configuration.
     *
     * @param array             $configs     An array of configuration settings
     * @param ContainerBuilder  $container   A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $loader->load('util.xml');

        $container->setParameter('fpn_tag.entity.tag.class', $config['model']['tag_class']);
        $container->setParameter('fpn_tag.entity.tagging.class', $config['model']['tagging_class']);

        $container->setAlias('fpn_tag.slugifier', $config['service']['slugifier']);
    }
}
