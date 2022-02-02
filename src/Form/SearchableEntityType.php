<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\CallbackTransformer;

class SearchableEntityType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function(Collection $collection) : array {
                return $collection->map(fn ($entity) => (string) $entity->getId() )->toArray();
            },
            function(array $values) use ($options) : Collection {
                if (empty($values)) {
                    return new ArrayCollection([]);
                }
                return new ArrayCollection($this->em->getRepository($options['class'])->findBY(['id' => $values]));
            },
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {

        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false;
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['preferred_choices'] = [];
        $view->vars['choices'] = $this->choices($form->getData());
        $view->vars['choice_translation_domain'] = false;
        $view->vars['full_name'] .= '[]';
        $view->vars['attr']['data-remote'] = $options['search'];
        $view->vars['attr']['data-value'] = $options['value_property'];
        $view->vars['attr']['data-label'] = $options['label_property'];
        $view->vars['attr']['data-search'] = $options['search_property'];
    }

    public function getBlockPrefix()
    {
        return 'choice'; // select field
    }

    private function choices(Collection $collection) : array
    {
        return $collection
            ->map(fn ($entity) => new ChoiceView($entity, (string) $entity->getId(), $entity->getName()) )
            ->toArray();
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setrequired('class');
        $resolver->setDefaults([
            'compound' => false,
            'multiple' => true,
            'search' => '/search',
            'value_property' => 'id',
            'label_property' => 'name',
            'search_property' => 'name'
        ]);
    }
}