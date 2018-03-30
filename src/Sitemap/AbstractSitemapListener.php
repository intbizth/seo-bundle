<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Sitemap;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;

abstract class AbstractSitemapListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerUrls($event->getUrlContainer());
    }

    /**
     * @param UrlContainerInterface $urls
     */
    abstract protected function registerUrls(UrlContainerInterface $urls): void;
}
