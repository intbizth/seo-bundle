<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method MetaSeoTranslationInterface getTranslation(): MetaSeoTranslationInterface
 */
class MetaSeo implements MetaSeoInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pathInfo;

    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

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
    public function getPathInfo(): ?string
    {
        return $this->pathInfo;
    }

    /**
     * @param string $pathInfo
     */
    public function setPathInfo(?string $pathInfo)
    {
        $this->pathInfo = $pathInfo;
    }

    /**
     * @return string
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName(?string $routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->getTranslation()->setTitle($title);
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description)
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * @return string
     */
    public function getFooter(): ?string
    {
        return $this->getTranslation()->getFooter();
    }

    /**
     * @param string $footer
     */
    public function setFooter(?string $footer)
    {
        $this->getTranslation()->setFooter($footer);
    }

    /**
     * @return string
     */
    public function getKeywords(): ?string
    {
        return $this->getTranslation()->getKeywords();
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(?string $keywords)
    {
        $this->getTranslation()->setKeywords($keywords);
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslation()
    {
        return new MetaSeoTranslation();
    }

    public function validate(ExecutionContextInterface $context)
    {
        /** @var self $obj */
        $obj = $context->getObject();

        if (!empty($obj->getPathInfo()) || !empty($obj->getRouteName())) {
            return;
        }

        $context->buildViolation('Route name or path info cannot be empty')
            ->atPath('pathInfo')
            ->addViolation();
    }
}
