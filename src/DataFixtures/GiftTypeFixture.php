<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Entity\GiftType;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserFixture
 * @package App\DataFixtures
 */
class GiftTypeFixture extends BaseFixture
{
    const giftType = [
        "Place",
        "Boisson",
        "Sachet de pop-corn",
    ];

    const fidelityCosts = [
        "10",
        "4",
        "6",
    ];

    const description = "Un(e) / gratuit(e) pour vous récompenser de votre fidélité !";

    private $index = 0;

    /**
     * @param ObjectManager $manager
     */
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(GiftType::class, 3, function(GiftType $giftType) {
            $giftType
                ->setName(self::giftType[$this->index])
                ->setDescription(str_replace("/", strtolower(self::giftType[$this->index]), self::description))
                ->setDuration(rand(2, 20))
                ->setFidelityCost(self::fidelityCosts[$this->index]);

            $this->index++;
        });

        $manager->flush();
    }
}
