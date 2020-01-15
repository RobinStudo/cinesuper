<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Entity\Voucher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;

class VoucherController extends AbstractController
{
    /**
     * @Route("/voucher/{id}", name="voucher")
     */
    public function vouchergenerate(Card $card, MailerService $mailerService)
    {  
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
    
                $em = $this->getDoctrine()->getManager();
                $em->persist($voucher);
                $em->persist($card);
                $em->flush();
            }
            //succes message
            $this->addFlash("success", "Vous avez le droit à  " . $nbVoucher. " place(s) gratuite(s). Nous avons envoyer votre bon par mail.");
           
            // instantiation send mail for free places
            $email = $card->getUser()->getEmail();

            // mailerService
            $mailerService->vouchergenerate($email, $nbVoucher);
        }
        return $this->redirectToRoute('dashboard') ;
    }
}
