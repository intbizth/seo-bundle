<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Provider;

interface LocaleProviderInterface
{
    /**
     * @return string[]
     */
    public function getAvailableLocalesCodes(): array;

    /**
     * @return string
     */
    public function getDefaultLocaleCode(): string;
}
