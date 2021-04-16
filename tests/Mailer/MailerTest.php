<?php

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class MailerTest extends TestCase{
    public function testConfirmationEmail(){
        $user = new User();
        $user->setEmail('john@doe.com');

        $mailerMock = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailerMock->expects($this->once())->method('send')
            ->with($this->callback(function ($subject){
                $messageSubject = (string) $subject;

                return strpos($messageSubject, "From: me@domain.com") !== false
                    && strpos($messageSubject, "Content-Type: text/html; charset=utf-8") !== false
                    && strpos($messageSubject, "Subject: Welcome to the micro post app!") !== false
                    && strpos($messageSubject, "To: john@doe.com") !== false
                    && strpos($messageSubject, "This is a message body") !== false;
            }));

        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock->expects($this->once())->method('render')
            ->with(
                'email/registration.html.twig',
                ['user' => $user]
            )->willReturn('This is a message body');

        $mailer = new Mailer($mailerMock, 'me@domain.com');

        $mailer->sendConfirmationEmail($user);
    }
}