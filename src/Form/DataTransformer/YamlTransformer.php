<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Yaml\Yaml;

class YamlTransformer implements DataTransformerInterface
{
    /**
     * The level where you switch to inline YAML.
     *
     * @var int
     */
    protected $inlineLevel;

    /**
     * @param int $inlineLevel
     */
    public function __construct($inlineLevel = 10)
    {
        $this->inlineLevel = $inlineLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return (array) Yaml::parse(preg_replace('/\t/', '    ', $value));
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return Yaml::dump($value, $this->inlineLevel);
    }
}
