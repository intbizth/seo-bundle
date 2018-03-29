<?php

declare(strict_types=1);

namespace Toro\SeoBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MetaSeoTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.title'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.description'
            ])
            ->add('footer', TextareaType::class, [
                'required' => false,
                'label' => 'toro_seo.form.meta_seo.footer'
            ])
        ;
    }
}
