<?php

namespace App\Form;

use App\Entity\Page;
use Infinite\FormBundle\Form\Type\PolyCollectionType;
use App\EventSubscriber\SlugifySubscriber;
use App\EventSubscriber\UpdatedDateSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\String\Slugger\SluggerInterface;

class PageType extends AbstractType
{
    public function __construct(private SluggerInterface $slugger)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('slug', TextType::class, [
                'required' => false,
            ])
            ->add('blocks', PolyCollectionType::class, [
                'types' => [
                    Block\ParagraphType::class,
                    Block\LinkType::class
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->addEventSubscriber(new UpdatedDateSubscriber())
            ->addEventSubscriber(new SlugifySubscriber($this->slugger, ['fields' => ['title']]))
        ;
    }

    public function getBlockPrefix()
    {
        return 'page';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
