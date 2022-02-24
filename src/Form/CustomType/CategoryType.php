<?php
namespace App\Form\CustomType;

use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class CategoryType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options_ = [
            'class' => $options['class'],
            'placeholder' => $options['placeholder'],
            'choice_label' => $options['choice_label'],
            'required' => $options['required'],
            'constraints' => $options['constraints'],
            'row_attr' => $options['sub_row_attr']      
        ];

        $name_ = $builder->getName();

        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($category) use ($name_) {
                    return [$name_ => $category];
                },
                function ($data) use ($name_) {
                    return $data[$name_];
                }
            ))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($options_, $name_) {
                $form = $event->getForm();
                $entity = $event->getData();
                
                if ($entity) {
                    $categories = self::getParents($entity);
                    $limit = count($categories)-1;
                    $repository = $this->em->getRepository($options_['class']);
                    foreach($categories as $key => $category) {
                        if ($key == $limit) {
                            continue;
                        }
                        $name = $key == $limit-1 ? $name_ : $name_. $key;
                        $mapped = $key == $limit-1 ?: false;
                        $data = $categories[$key+1]; 
                        $form->add($name, EntityType::class, array_merge($options_, [
                            'choices' => $repository->getChildren($category, true),
                            'data' => $data
                        ]));
                    }
                } else {
                    $form->add($name_, EntityType::class, array_merge($options_, [
                        'query_builder' => function (EntityRepository $er) {
                            $root = $er->findOneByIdentifier('CATEGORIES_ROOT');
                            return $er->getChildrenQueryBuilder($root, true);
                        },
                    ]));
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options_, $name_) {
                $form = $event->getForm();
                $data = $event->getData();
                $f = $this->requestStack->getCurrentRequest()->get('f');
                $field = empty($f) ? $name_ : NULL; 
                $value = NULL;
                $categories = [];
                $i = 0;
                $position = 0;
                $repository = $this->em->getRepository($options_['class']);

                foreach($data as $name => $val) {
                    if ($name == $f || $name == $field) { 
                        $value = $val;
                        $position = $i;
                    }
                    $categories[] = $val; 
                    $i++;
                }

                if (!$value && $position == 0) {
                    $category = $repository->findOneByIdentifier('CATEGORIES_ROOT');
                }
                elseif (!$value && $position) {   
                    $category = $repository->findOneById($categories[$position-1]);
                } else {
                    $category = $repository->findOneById($value);
                }

                foreach($data as $key => $val) {
                    unset($data[$key]);
                }

                foreach(array_keys($form->all()) as $name) {
                    $form->remove($name);  
                }

                $children = $repository->getChildren($category, true);
                $haschildren = !empty($children) ?: false;
                if (!empty($f) && !$haschildren) { $data['@@error'] = ''; }
                $categories = self::getParents($category);
                $nb_categories = count($categories);
                $limit = $haschildren ? $nb_categories  : $nb_categories - 1;

                for($i=1; $i < $nb_categories; $i++) {
                    $name = $i ==  $limit ? $name_ : $name_. $i-1;
                    $data[$name] = $categories[$i]->getId();
                }

                for($i=0; $i < $limit; $i++) {
                    $choices = $repository->getChildren($categories[$i], true);
                    $name = $i == $limit-1 ? $name_ : $name_ . $i;
                    $mapped = $name == $name_ ?: false;
                    $entity = $name == $name_ ? NULL : $categories[$i+1];

                    $form->add($name, EntityType::class, array_merge($options_, [
                        'choices' => $choices,
                        'data' => $entity
                    ]));
                }

                $event->setData($data);
            }
        );
    }

    private static function getParents($category)
    {
        $fct = function($category, $array = []) use (&$fct) {
            $array[] = $category;
            if ($category->getParent() != null) {
                $array = $array + $fct($category->getParent(), $array);
            }
            return $array;
        };

        return array_reverse($fct($category));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'placeholder' => 'Choose an option',
            'choice_label' => 'title',
            'required' => true,
            'constraints' => new NotBlank(),
            'sub_row_attr' => ['class' => 'mb-3 category']
        ]);
    }
}