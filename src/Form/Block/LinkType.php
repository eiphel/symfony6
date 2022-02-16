<?php

namespace App\Form\Block;

use App\Entity\Block\Link;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class LinkType extends AbstractBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('title')
            ->add('url')
            ->add('content')
        ;
    }

    public function getBlockPrefix()
    {
        return 'link';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
            'model_class' => Link::class,
        ]);
    }
}