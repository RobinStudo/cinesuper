<?php

namespace App\Service;


use App\Entity\Gift;
use App\Entity\User;

/**
 * Class GiftService
 * @package App\Service
 */
class GiftService
{
    public function generateSerial(User $user, Gift $gift)
    {
        $serialNumber = $user->getLastName().uniqid();

        $gift->setSerial($serialNumber);
    }
}