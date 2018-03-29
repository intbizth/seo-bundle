<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Toro\SeoBundle\Provider\LocaleProviderInterface;

final class StaticRouteSitemapListener extends AbstractSitemapListener
{
    /**
     * @var array
     */
    private $routes;

    public function __construct(UrlGeneratorInterface $urlGenerator, LocaleProviderInterface $localeProvider, $routes = [])
    {
        parent::__construct($urlGenerator, $localeProvider);
        $this->routes = $routes;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
       return 'static';
    }

    /**
     * @inheritdoc
     */
    public function process()
    {
        foreach ($this->routes as $route) {
            $this->addUrl($this->createUrl([
                'route' => $route['route'],
                'parameters' => $route['parameters'],
                'changefreq' => $route['changefreq'],
                'priority' => $route['priority'],
            ]));
        }
    }
}
