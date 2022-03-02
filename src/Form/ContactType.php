<?php

namespace App\Form;

use App\Entity\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', null, [
                'required' => true
            ])
            ->add('emailFrom', null, [
                'required' => true
            ])
            ->add('body', null, [
                'required' => true
            ])
            ->add('firstName', null, [
                'required' => true
            ])
            ->add('lastName', null, [
                'required' => true
            ])
            ->add('phone', null, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'an arbitrary string used to generate the value of the token',
        ]);
    }
}
