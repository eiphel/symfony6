<?php

namespace App\Message;

use App\Entity\Email;

final class SendEmailMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

//     private $name;
//
//     public function __construct(string $name)
//     {
//         $this->name = $name;
//     }
//
//    public function getName(): string
//    {
//        return $this->name;
//    }

    private $emailId;

    public function __construct(Email $email)
    {
        $this->emailId = $email->getId();
    }

    public function getEmailId(): int
    {
        return $this->emailId;
    }
}
