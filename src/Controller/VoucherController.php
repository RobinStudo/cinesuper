<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Voucher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VoucherController extends AbstractController
{
    /**
     * @Route("/voucher/{id}", name="voucher")
     */
    public function vouchergenerate(Card $card)
    {
          if($card->getFidelity() >=10) 
         { 
            $nbplace=$card->getFidelity();
            $nbVoucher= intdiv($nbplace,10);
            for ($i=1; $i <= $nbVoucher; $i++)
             { 
             

                $voucher = new Voucher();
                      
                // changer le type de serial number en chaine 
                $serialNumber =$card->getUser()->getLastName().uniqid();
    
                $voucher->setSerial($serialNumber);
    
                $voucher->setExpiredAt(new \DateTime( '6 months' ));
    
                // add voucher to Card
                $card->addVoucher($voucher);  
    
                $em = $this->getDoctrine()->getManager();
                $em->persist($voucher);
                $em->persist($card);
                $em->flush();
    
                $this->addFlash("success", "le bon de fideleté est generer avec succee ");
            }
             
        }
          return $this->redirectToRoute('dashboard') ;
       
    }



}
