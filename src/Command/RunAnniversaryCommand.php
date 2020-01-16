<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Voucher;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunAnniversaryCommand extends Command
{
    protected static $defaultName = 'runAnniversary';
    private $userRepository;
    private $em;
    private $container;

    public function __construct(UserRepository $userRepository, MailerService $mailerService, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->mailerService = $mailerService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAll();
        $now = new \Datetime('now');

        foreach ($users as $user)
        {
            if ( $now->format('d/m/Y') === $user->getBirthdate()->format('d/m/Y'))
            {
                // Generation du voucher pour l'anniversaire de l'utilisateur
                $voucher = new Voucher;
                $serialNumber = $user->getLastName().uniqid();

                $voucher->setSerial($serialNumber);
                $voucher->setCard($user->getCard());
                $voucher->setExpiredAt(new \DateTime( '6 months' ));

                $this->em->persist($voucher);
                $this->em->flush();

                // Envoi du mail
                $this->mailerService->sendAnniversary($user->getEmail(),"Bravo tu as reçu 1 place gratuite pour ton anniversaire.");

            }
        }

        $io->success('Les vouchers ont bien été générés.');

        return 0;
    }
}
