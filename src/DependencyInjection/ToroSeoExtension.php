<?php

declare(strict_types=1);

namespace Toro\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Toro\SeoBundle\Event\ClearMetaSeoCache;
use Toro\SeoBundle\Sitemap\StaticRouteSitemapListener;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ToroSeoExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('toro', $config['driver'], [], $container);

        $container->setParameter('toro_seo.sitemap_routing', $config['sitemap_routing']);
        $container->setParameter('toro_seo.ignore_request_attrs', $config['ignore_request_attrs']);

        $loader->load('services.xml');

        $this->registerMetaSeoRenderer($config, $container);
        $this->registerMetaSeoCache($config, $container);
        $this->registerSitemapLocaleProvider($config, $container);
    }

    private function registerMetaSeoRenderer(array $config, ContainerBuilder $container)
    {
        if ('toro_seo.twig_extension.render_meta_extension' !== $config['renderer']['service']) {
            return;
        }

        if (empty($config['renderer']['image_provider'])) {
            throw new InvalidConfigurationException('`image_provider` must be config under `renderer` if u use `toro_seo.twig_extension.render_meta_extension` renderer.');
        }

        $twigRenderer = $container->getDefinition('toro_seo.renderer.twig');
        $twigRenderer->setArgument(2, new Reference($config['renderer']['image_provider']));
        $container->setDefinition('toro_seo.renderer.twig', $twigRenderer);
    }

    private function registerMetaSeoCache(array $config, ContainerBuilder $container)
    {
        if ('toro_seo.twig_extension.render_meta_extension' !== $config['renderer']['service']) {
            return;
        }

        if (empty($config['renderer']['cache'])) {
            return;
        }

        $twigRenderer = $container->getDefinition('bonn_seo.renderer.twig');
        $twigRenderer->setArgument(3, new Reference($config['renderer']['cache']));
        $container->setDefinition('bonn_seo.renderer.twig', $twigRenderer);

        $clearCacheDefinition = new Definition(ClearMetaSeoCache::class, [
            new Reference($config['renderer']['cache'])
        ]);

        $clearCacheDefinition->addTag('doctrine.event_subscriber', [
            'connection' => 'default'
        ]);

        $container->setDefinition('toro_seo.event.meta_seo_cache_clear_subscriber', $clearCacheDefinition);
    }

    private function registerSitemapLocaleProvider(array $config, ContainerBuilder $container)
    {
        if ('toro_seo.locale_provider.configuration' !== $config['sitemap_locale']['service']) {
            $container->setAlias('toro_seo.locale_provider', $config['sitemap_locale']['service']);
            return;
        }

        if (empty($config['sitemap_locale']['default_locale']) || empty($config['sitemap_locale']['all_locales'])) {
            throw new InvalidConfigurationException('`default_locale` and `all_locales` must be config under `sitemap_locale` if u use `toro_seo.locale_provider.configuration` provider.');
        }

        $localeProvider = $container->getDefinition('toro_seo.locale_provider.configuration');
        $localeProvider->setArgument(0, $config['sitemap_locale']['default_locale']);
        $localeProvider->setArgument(1, $config['sitemap_locale']['all_locales']);
        $container->setDefinition('toro_seo.locale_provider.configuration', $localeProvider);
        $container->setAlias('toro_seo.locale_provider', 'toro_seo.locale_provider.configuration');
    }
}
