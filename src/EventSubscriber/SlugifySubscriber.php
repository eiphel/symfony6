<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\String\Slugger\SluggerInterface;


class SlugifySubscriber implements EventSubscriberInterface
{
    public function __construct(private SluggerInterface $slugger,private array $options = [])
    {

    }   

    public function onFormSubmit(FormEvent $event)
    {
        $data = $event->getData();
        //$form = $event->getForm();

        $slug = $data['slug'];
        $slug = trim($slug);

        if (empty($slug)) {

            $f = [];

            foreach($this->options['fields'] as $field) {
                array_push($f, $data[$field]);
            }

            $slug = implode('-', $f);

            $data['slug'] = strtolower($this->slugger->slug($slug));

            $event->setData($data); 
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onFormSubmit',
        ];
    }
}
