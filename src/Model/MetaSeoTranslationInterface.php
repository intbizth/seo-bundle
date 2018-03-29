<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Model;

use Toro\Bundle\MediaBundle\Model\FileAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MetaSeoTranslationInterface extends ResourceInterface
{
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
