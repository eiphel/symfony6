<?php

namespace App\Form;

use App\Entity\Region;
use App\Form\DepartmentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class RegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('departments', CollectionType::class, [
                'entry_type' => DepartmentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false // false addDepartment et non set
            ])
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort($view['departments']->children, function (FormView $a, FormView $b) {
            $objectA = $a->vars['data'];
            $objectB = $b->vars['data'];

            $posA = $objectA->getPosition();
            $posB = $objectB->getPosition();

            if ($posA == $posB) {
                return 0;
            }

            return ($posA < $posB) ? -1 : 1;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Region::class,
        ]);
    }
}
