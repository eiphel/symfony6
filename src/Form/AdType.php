<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\Department;
use App\EventSubscriber\SaveImagesSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RequestStack;

class AdType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url, private ParameterBagInterface $parameter, private RequestStack $requestStack)
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
            ->add('upload', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'data-remote' => $this->url->generate('upload_images'),
                    'accept' => 'image/png, image/gif, image/jpeg'
                ]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => ['data-url' => $this->parameter->get('images_url')],
            ])
            ->add('removed_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'required' => false,
                'label' => false,
                'allow_add' => true,
                'mapped' => false,
            ])
            ->addEventSubscriber(new SaveImagesSubscriber($this->parameter, $this->requestStack))
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort($view['images']->children, function (FormView $a, FormView $b) {
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
            'data_class' => Ad::class,
        ]);
    }
}
