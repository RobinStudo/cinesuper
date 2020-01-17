<?php

namespace App\Service;

use App\Repository\EventRepository;

/**
 * Class EventService
 * @package App\Service
 */
class EventService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var \DateTime
     */
    private $currentDate;

    /**
     * EventService constructor.
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->currentDate = new \DateTime();
    }

    /**
     * @return float|int
     */
    public function getMultiplicatorWhenEvent()
    {
        $events = $this->eventRepository->findAll();

        $multiplicator = 0;

        if ($events) {
            forEach($events as $event) {
                if ($this->currentDate >= $event->getStartAt() && $this->currentDate <= $event->getEndAt()) {
                    $multiplicator += $event->getMultiplicateur();
                }
            }
        }
        else {
            $multiplicator = 1;
        }

        return $multiplicator;
    }

    // if($card->getFidelity() >= 10)
    // {
    //     $nbplace = $card->getFidelity();
    //     $nbVoucher = intdiv($nbplace,10);
    //     $nbPlacesRestantes = $nbplace - $nbVoucher * 10;

    //     for ($i=1; $i <= $nbVoucher; $i++)
    //     {
    //         $voucher = new Voucher();

    //         // changer le type de serial number en chaine
    //         $serialNumber =$card->getUser()->getLastName().uniqid();

    //         $voucher->setSerial($serialNumber);

    //         $voucher->setExpiredAt(new \DateTime( '6 months' ));

    //         // add voucher to Card
    //         $card->addVoucher($voucher);
    //         $card->setFidelity($nbPlacesRestantes);

    //         $em->persist($voucher);
    //         $em->persist($card);
    //         $em->flush();
    //     }

    //     // instantiation send mail for free places
    //     $email = $card->getUser()->getEmail();

    //     // mailerService
    //     $mailerService->vouchergenerate($email, $nbVoucher);
    // }
    // return $this->redirectToRoute('easyadmin');

}
