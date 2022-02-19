<?php

namespace App\Form;

use App\Entity\Category;
use App\Form\DataTransformer\CategoryToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class CategoryType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack, private CategoryToNumberTransformer $transformer)
    {

    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['data'];

        $builder
            ->add('title')
            ->add('slug')
            ->add('description')
        ;

        if (!$category->getId()) {
            $builder->add('parent', HiddenType::class, [
                'data' => $this->requestStack->getCurrentRequest()->get('id'),
            ]);
        } else {
            $builder->add('parent', ChoiceType::class, [
                'choices' => $this->getData($category)
            ]);
        }

        $builder->get('parent')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    protected function getData($category)
    {
        $fct = function($children, $array = [], $i = 1) use (&$fct) {
            foreach($children as $child) {
                $repeat = str_repeat("-", $i*2);
                $repeat = empty($repeat) ? '' : $repeat . ' ';
                $array[$child['id']] = $repeat . $child['title'];
                if (!empty($child['__children'])) {
                    $j = $i+1;
                   $array = $array + $fct($child['__children'], $array, $j);
                }
            }
            return $array;
        };

        $repo = $this->em->getRepository(Category::class);
        $root = $repo->findOneByIdentifier('CATEGORIES_ROOT');
        $children = $repo->childrenHierarchy($root);
        $exclude = $repo->childrenHierarchy($category);
        
        $children = [$root->getId() => $root->getTitle()] + $fct($children);
        $exclude = [$category->getId() => ''] + $fct($exclude);

        return array_flip(array_diff_key($children, $exclude));
    }
}
