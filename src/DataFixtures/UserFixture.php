<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Service\UserService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

/**
 * Class UserFixture
 * @package App\DataFixtures
 */
class UserFixture extends BaseFixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserFixture constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserService $userService
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserService $userService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userService = $userService;
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 1, function(User $user) {
            $user
                ->setEmail("admin@cinesuper.com")
                ->setPassword($this->passwordEncoder->encodePassword($user, "12345678"))
                ->setFirstName("David")
                ->setLastName("Hasselhoff")
                ->setEnabled(1)
                ->setRoles(["ROLE_ADMIN"])
                ->setBirthdate(new \Datetime('now'))
                ->setCard($this->userService->generateCard($user));
        });

        $manager->flush();
    }
}
