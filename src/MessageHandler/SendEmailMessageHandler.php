<?php

namespace App\MessageHandler;

use App\Entity\Email;
use App\Message\SendEmailMessage;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    public function __construct(private EntityManagerInterface  $em, private SendEmailService $emailService)
    {

    }

    public function __invoke(SendEmailMessage $message)
    {
        // do something with your message
        $email = $this->em->find(Email::class, $message->getEmailId());

        if ($email) {
            $this->emailService->send($email);
            $email->setSend(true);
            $this->em->persist($email);
            $this->em->flush();
        }
        
    }
}
