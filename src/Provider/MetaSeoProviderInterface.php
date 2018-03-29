<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Provider;

use Toro\SeoBundle\Model\MetaSeoInterface;
use Symfony\Component\HttpFoundation\Request;

interface MetaSeoProviderInterface
{
    /**
     * @param Request $request
     * @return MetaSeoInterface|null
     */
    public function getMetaFromRequest(Request $request): ?MetaSeoInterface;
}
