<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\CustomType\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
//use Symfony\Component\HttpFoundation\Request;
//$request = Request::createFromGlobals();

class ProductType extends AbstractType
{
    public function __construct(private CategoryRepository $repository, private RequestStack $requestStack) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description')
            ->add('price')
            ->add('category', CategoryType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
