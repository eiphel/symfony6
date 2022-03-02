<?php

namespace App\Service;

use App\Entity\Email as Contact;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class SendEmailService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function send(Contact $contact) : void
    {
        $email = (new Email())
        ->from(new Address($contact->getEmailFrom(), $contact->getFirstName()))
        ->to('you@example.com')
        ->subject($contact->getSubject())
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }
}
