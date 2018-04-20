<?php

declare(strict_types=1);

namespace Toro\SeoBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toro_seo');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->arrayNode('renderer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('service')->defaultValue('toro_seo.twig_extension.render_meta_extension')->end()
                        ->scalarNode('image_provider')->defaultNull()->end()
                        ->scalarNode('cache')->defaultValue('seo_meta_data.cache')->end()
                    ->end()
                ->end()
                ->arrayNode('ignore_request_attrs')
                    ->scalarPrototype()
                        ->defaultValue(['template', '_locale', '_sylius'])
                    ->end()
                ->end()
                ->arrayNode('sitemap_routing')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('route')->isRequired()->end()
                            ->variableNode('parameters')->defaultValue([])->end()
                            ->floatNode('priority')->defaultValue(0.7)->end()
                            ->enumNode('changefreq')->values(array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never"))->defaultValue("daily")->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('sitemap_locale')
                    ->addDefaultsIfNotSet()
                    ->children()
                         ->scalarNode('service')->defaultValue('toro_seo.locale_provider.configuration')->end()
                         ->scalarNode('default_locale')->end()
                         ->arrayNode('all_locales')->scalarPrototype()->end()
                     ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
