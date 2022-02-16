<?php

namespace App\Form\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

abstract class AbstractBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier')
            ->add('position', HiddenType::class)
            ->add('_type', HiddenType::class, [
                'data'   => $this->getBlockPrefix(),
                'mapped' => false
            ]
        );
    }
}