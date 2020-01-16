<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class GiftService
{
    public function __construct()
    {

    }

    public function generateVoucher(User $user, MailerService $mailerService, Request $request)
    {


        /*public function vouchergenerate(User $user, MailerService $mailerService, Request $request)
    {
        /*$card = $user->getCard();

        if ($request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();

            $card->setFidelity($card->getFidelity() + $request->request->get('ticketNumber'));
            $em->flush();

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
    }*/


    }
}
