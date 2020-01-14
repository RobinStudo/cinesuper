<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PictureService
 * @package App\Service
 */
class PictureService
{
    /**
     * @var PictureRepository
     */
    private $pictureRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PictureService constructor.
     * @param PictureRepository $pictureRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PictureRepository $pictureRepository, EntityManagerInterface $em) {
        $this->pictureRepository = $pictureRepository;
        $this->em = $em;
    }

    /**
     * @param User $user
     */
    public function deleteLastPicture(User $user) {
        $lastPicture = $this
            ->pictureRepository
            ->find([
                "id" => $user->getPicture()->getId()
            ]);

        $user->setPicture(null);

        $this->em->remove($lastPicture);
    }
}