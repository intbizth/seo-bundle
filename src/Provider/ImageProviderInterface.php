<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Provider;

use Toro\SeoBundle\Model\MetaSeoInterface;

interface ImageProviderInterface
{
    /**
     * @param MetaSeoInterface $metaSeo
     * @return null|string absolute_url
     */
    public function getImagePath(MetaSeoInterface $metaSeo): ?string;
}
