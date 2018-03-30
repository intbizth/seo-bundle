<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Sitemap;

use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Toro\SeoBundle\Provider\LocaleProviderInterface;

final class StaticRouteSitemapListener extends AbstractSitemapListener
{
    const SECTION = 'static';

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var array
     */
    private $routes;

    public function __construct(UrlGeneratorInterface $urlGenerator, LocaleProviderInterface $localeProvider, $routes = [])
    {
        parent::__construct($urlGenerator);
        $this->localeProvider = $localeProvider;
        $this->routes = $routes;
    }

    /**
     * @inheritdoc
     */
    public function registerUrls(UrlContainerInterface $urls): void
    {
        foreach ($this->routes as $route) {
            $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
            $isMultiLang = 2 <= count($availableLocalesCodes);

            $parameters = $isMultiLang ? array_merge($route['parameters'], ['_locale' => $this->localeProvider->getDefaultLocaleCode()]) : $route['parameters'];
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    $route['route'],
                    $parameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                new \DateTime(),
                $route['changefreq'],
                $route['priority']
            );

            if (!$isMultiLang) {
                $urls->addUrl($url, self::SECTION);
                continue;
            }

            $url = new GoogleMultilangUrlDecorator($url);
            foreach ($this->localeProvider->getAvailableLocalesCodes() as $localeCode) {
                $url->addLink($this->urlGenerator->generate(
                    $route['route'],
                    array_merge($parameters, ['_locale' => $localeCode]),
                    UrlGeneratorInterface::ABSOLUTE_URL
                ), $localeCode);
            }

            $urls->addUrl($url, self::SECTION);
        }
    }
}
