<?php
namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;
 
class UserConnectedEvent extends Event
{
    const NAME = 'user.connected.event';
 
    protected $user;
    protected $foo;
 
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->foo = 'foo';
    }
 
    public function getFoo()
    {
        return $this->foo;
    }

    public function getUser()
    {
        return $this->user;
    }
}