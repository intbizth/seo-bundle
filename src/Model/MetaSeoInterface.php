<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface MetaSeoInterface extends ResourceInterface, TimestampableInterface, TranslatableInterface
{
    /**
     * @return string
     */
    public function getPathInfo(): ?string;

    /**
     * @param string $pathInfo
     */
    public function setPathInfo(?string $pathInfo);

    /**
     * @return string
     */
    public function getRouteName(): ?string;

    /**
     * @param string $routeName
     */
    public function setRouteName(?string $routeName);

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * @param string $title
     */
    public function setTitle(?string $title);

    /**
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     */
    public function setDescription(?string $description);

    /**
     * @return string
     */
    public function getFooter(): ?string;

    /**
     * @param string $footer
     */
    public function setFooter(?string $footer);

    /**
     * @return string
     */
    public function getKeywords(): ?string;

    /**
     * @param string $keywords
     */
    public function setKeywords(?string $keywords);
}
