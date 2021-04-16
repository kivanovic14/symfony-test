<?php


namespace App\Mailer;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Mailer extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $mailFrom;

    public function __construct(\Swift_Mailer $mailer, string $mailFrom)
    {

        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
    }

    public function sendConfirmationEmail(User $user)
    {

        $body = $this->render('email/registration.html.twig', [
            'user' => $user,
        ]);
        $message = (new \Swift_Message())
            ->setSubject('Welcome to the micro-post app!')
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body,'text/html');

        $this->mailer->send($message);
    }
}