<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Twig\Extension;

use Toro\SeoBundle\Renderer\RendererInterface;

final class RenderMetaExtension extends \Twig_Extension
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('toro_seo_render', [$this->renderer, 'render'], ['is_safe' => ['html']]),
        ];
    }
}
