<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class UpdatedDateSubscriber implements EventSubscriberInterface
{
    public function onFormSubmit(FormEvent $event)
    {
        $data = $event->getData();
        //$form = $event->getForm();

        $data->setUpdatedAt(new \DateTimeImmutable);
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onFormSubmit',
        ];
    }
}