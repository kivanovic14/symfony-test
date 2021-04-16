<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    const NAME = 'user.register'; // unique string in
    // symfony system that would identify this particular
    // event
    /**
     * @var User
     */
    private $registeredUser;

    public function __construct(User $registeredUser)
    {

        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUser;
    }
}