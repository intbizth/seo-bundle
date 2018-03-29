<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Renderer;

use Toro\SeoBundle\Model\MetaSeoInterface;
use Toro\SeoBundle\Provider\ImageProviderInterface;
use Toro\SeoBundle\Provider\MetaSeoProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TwigRenderer implements RendererInterface
{
    const DEFAULT_TEMPLATE = '@ToroSeo/_web_meta_seo.html.twig';

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ImageProviderInterface
     */
    private $imageProvider;

    /**
     * @var MetaSeoProviderInterface
     */
    private $provider;

    /**
     * @var OptionsResolver
     */
    private $optionResolver = [];

    public function __construct(MetaSeoProviderInterface $provider, \Twig_Environment $twig, ImageProviderInterface $imageProvider = null)
    {
        $this->provider = $provider;
        $this->imageProvider = $imageProvider ?: null;
        $this->twig = $twig;
        $this->optionResolver = new OptionsResolver();
        $this->optionResolver
            ->setDefaults([
                'template' => self::DEFAULT_TEMPLATE,
                'default_description' => null,
                'default_keywords' => null,
                'default_footer' => null,
                'default_image_url' => null,
                'options' => []
            ])
            ->setRequired('default_title')
            ->setAllowedTypes('default_title', ['string'])
            ->setAllowedTypes('default_description', ['string', 'null'])
            ->setAllowedTypes('default_keywords', ['string', 'null'])
            ->setAllowedTypes('default_footer', ['string', 'null'])
            ->setAllowedTypes('default_image_url', ['string', 'null'])
            ->setAllowedTypes('options', ['array'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, array $options): string
    {
        $meta = $this->provider->getMetaFromRequest($request);

        $options = $this->optionResolver->resolve($options);

        $context = [
            'title' => $options['default_title'],
            'description' => $options['default_description'],
            'keywords' => $options['default_keywords'],
            'footer' => $options['default_footer'],
            'image_url' => $options['default_image_url'],
        ];

        if ($meta instanceof MetaSeoInterface) {
            $context = [
                'title' => $meta->getTitle() ?: $options['default_title'],
                'description' => $meta->getDescription() ?: $options['default_description'],
                'keywords' => $meta->getKeywords() ?: $options['default_keywords'],
                'footer' => $meta->getFooter() ?: $options['default_footer'],
                'image_url' => $this->imageProvider ? $this->imageProvider->getImagePath($meta) ?: $options['default_image_url'] : '',
            ];
        }

        return $this->twig->render($options['template'], [
            'toro_seo' => $context,
            'options' => $options['options']
        ]);
    }
}
