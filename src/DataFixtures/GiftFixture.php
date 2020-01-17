<?php

namespace App\DataFixtures;

use App\Entity\Gift;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GiftFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $title = ['Une place gratuite', 'Une boisson', 'Un mars', 'Un twix', 'Un bounty', 'Un snickers'];
        $picture = ['voucher.jpg', 'boisson.jpg', 'mars.jpg', 'twix.jpg', 'bounty.jpg', 'snickers.jpg'];
        $nbFidelity = [10, 2, 3, 3, 3, 3];

        for ($i=0; $i < 6; $i++) { 
            $gift = new Gift;
            $gift->setTitle($title[$i])
                 ->setPicture($picture[$i])
                 ->setNbFidelity($nbFidelity[$i]);


            $manager->persist($gift);
        }

        $manager->flush();
    }
}
