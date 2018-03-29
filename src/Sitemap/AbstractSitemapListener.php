<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Sitemap;

use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Toro\SeoBundle\Provider\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

abstract class AbstractSitemapListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var OptionsResolver
     */
    protected $optionResolver;

    /**
     * @var array
     */
    private $urls = [];

    public function __construct(UrlGeneratorInterface $urlGenerator, LocaleProviderInterface $localeProvider)
    {
        $this->urlGenerator = $urlGenerator;
        $this->localeProvider = $localeProvider;
        $this->optionResolver = new OptionsResolver();
        $this->optionResolver
            ->setDefaults([
                'changefreq' => 'weekly',
                'priority' => 0.8,
                'parameters' => [],
                'updated_at' => null,
                'multi_lang' => true
            ])
            ->setRequired('route')
        ;
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
        $this->process();
        $this->registerUrls($event->getUrlContainer());
    }

    /**
     * @return string
     */
    abstract function getName(): string;

    /**
     * Call `addUrl` in this method;
     *
     * @return void
     */
    abstract function process();

    /**
     * @param Url $url
     */
    protected function addUrl(Url $url): void
    {
        $this->urls[] = $url;
    }

    /**
     * @param array $userOptions
     * @return Url
     */
    protected function createUrl(array $userOptions): Url
    {
        $options = $this->optionResolver->resolve($userOptions);

        $parameters = $options['multi_lang'] ? array_merge($options['parameters'], ['_locale' => $this->localeProvider->getDefaultLocaleCode()]) : $options['parameters'];
        $url = new UrlConcrete(
            $this->urlGenerator->generate(
                $options['route'],
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $options['updated_at'] ?: new \DateTime(),
            $options['changefreq'],
            $options['priority']
        );

        if (false === $options['multi_lang']) {
            return $url;
        }

        $url = new GoogleMultilangUrlDecorator($url);
        foreach ($this->localeProvider->getAvailableLocalesCodes() as $localeCode) {
            $url->addLink($this->urlGenerator->generate(
                $options['route'],
                array_merge($options['parameters'], ['_locale' => $localeCode]),
                UrlGeneratorInterface::ABSOLUTE_URL
            ), $localeCode);
        }

        return $url;
    }

    /**
     * @param UrlContainerInterface $urls
     */
    private function registerUrls(UrlContainerInterface $urls): void
    {
        foreach ($this->urls as $url) {
            $urls->addUrl(
                $url,
                $this->getName()
            );
        }
    }
}
