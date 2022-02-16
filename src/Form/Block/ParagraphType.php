<?php

namespace App\Form\Block;

use App\Entity\Block\Paragraph;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ParagraphType extends AbstractBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('content')
        ;
    }

    public function getBlockPrefix()
    {
        return 'paragraph';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paragraph::class,
            'model_class' => Paragraph::class,
        ]);
    }
}