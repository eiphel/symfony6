<?php

namespace App\EventSubscriber;

use App\Event\UserConnectedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            UserConnectedEvent::NAME => 'onLogin',
        ];
    }

    public function onLogin(UserConnectedEvent $event)
    {
        $event->getUser();
        $event->getFoo();
    }
}