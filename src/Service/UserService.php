<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\User;

class UserService{

    public function generateToken( User $user ){
        $token = bin2hex( random_bytes( 64 ) );
        $expire = new \DateTime( '1 day' );

        $user->setToken( $token );
        $user->setTokenExpire( $expire );
    }

    public function generateCard( User $user ): Card{
        $card = new Card();
        $number = time() . mt_rand( 1000, 9999 );
        $card->setNumber( $number );
        $user->setCard( $card );
        return $card;
    }
}
