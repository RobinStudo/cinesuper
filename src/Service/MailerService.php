<?php
namespace App\Service;

use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use App\Entity\User;

class MailerService{
    private $urlGenerator;
    private $mailer;

    public function __construct( UrlGeneratorInterface $urlGenerator, Swift_Mailer $mailer ){
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
    }

    public function sendActivationMail( User $user ){
        $url = $this->urlGenerator->generate( 'user_activate', array(
            'token' => $user->getToken(),
        ), UrlGenerator::ABSOLUTE_URL);

        $text = 'Bonjour, veuillez activer votre compte : ' . $url;

        $this->send( $user->getEmail(), $text );
    }

    private function send( $email, $text ){
        $message = new Swift_Message();
        $message->setFrom( 'no-reply@cinesuper.com' );
        $message->setTo( $email );
        $message->setBody( $text );

        $this->mailer->send( $message );
    }
}
