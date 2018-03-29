<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Provider;

final class ConfigurationLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var string
     */
    private $defaultLocale = "";

    /**
     * @var array
     */
    private $allLocales = [];

    public function __construct(string $defaultLocale = "", array $allLocales = [])
    {
        $this->defaultLocale = $defaultLocale;
        $this->allLocales = $allLocales;
    }

    public function getAvailableLocalesCodes(): array
    {
        return $this->allLocales;
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocale;
    }
}
