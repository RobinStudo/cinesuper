<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Voucher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;

class VoucherController extends AbstractController
{
    /**
     * @Route("/voucher/{id}", name="addFidelity")
     */
<<<<<<< HEAD
    public function vouchergenerate(Card $card, MailerService $mailerService, Request $request, Event $event)
    {  
=======
    public function vouchergenerate(User $user, MailerService $mailerService, Request $request)
    {
        $card = $user->getCard();
>>>>>>> master
        if ($request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();

            $card->setFidelity($card->getFidelity() + $request->request->get('ticketNumber'));
            $em->flush();

            $begin = $event->startAt;
            $end = $event->endAt;

            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($begin, $interval ,$end);
          
            foreach($daterange as $date){
                echo $date->format("Ymd") . "<br>";
            }

            if($card->getFidelity() >= 10) 
            { 
                $nbplace = $card->getFidelity();
                $nbVoucher = intdiv($nbplace,10);
                $nbPlacesRestantes = $nbplace - $nbVoucher * 10;

                for ($i=1; $i <= $nbVoucher; $i++)
                {
                    $voucher = new Voucher();

                    // changer le type de serial number en chaine
                    $serialNumber =$card->getUser()->getLastName().uniqid();

                    $voucher->setSerial($serialNumber);

                    $voucher->setExpiredAt(new \DateTime( '6 months' ));

                    // add voucher to Card
                    $card->addVoucher($voucher);
                    $card->setFidelity($nbPlacesRestantes);

                    $em->persist($voucher);
                    $em->persist($card);
                    $em->flush();
                }

                // instantiation send mail for free places
                $email = $card->getUser()->getEmail();

                // mailerService
                $mailerService->vouchergenerate($email, $nbVoucher);
            }
            return $this->redirectToRoute('easyadmin');
        }

        return $this->render('voucher/addFidelity.html.twig', [
            'card' => $card,
        ]);
    }

    
}
