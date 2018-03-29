<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Renderer;

use Symfony\Component\HttpFoundation\Request;

interface RendererInterface
{
    /**
     * @param Request $request
     * @param array $options
     * @return string
     */
    public function render(Request $request, array $options): string;
}
