<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TimestampableTrait;

class MetaSeoTranslation extends AbstractTranslation implements MetaSeoTranslationInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

     /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $footer;
    
    /**
     * @var string
     */
    protected $keywords;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFooter(): ?string
    {
        return $this->footer;
    }

    /**
     * @param string $footer
     */
    public function setFooter(?string $footer)
    {
        $this->footer = $footer;
    }

    /**
     * @return string
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(?string $keywords)
    {
        $this->keywords = $keywords;
    }
}
