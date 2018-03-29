<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MetaSeoType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pathInfo', TextType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.path_info'
            ])
            ->add('routeName', TextType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.route_name'
            ])
            ->add('parameters', YamlType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.parameters',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'label' => false,
                'entry_type' => MetaSeoTranslationType::class,
            ])
        ;
    }
}
