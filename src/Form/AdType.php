<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityRepository;


class AdType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('categories', SearchableEntityType::class, [
                'class' => AdCategory::class,
                'search' => $this->url->generate('api_categories'),
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
