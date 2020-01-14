<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VoucherController extends AbstractController
{
    /**
     * @Route("/voucher/{id}", name="voucher")
     */
    public function vouchergenerate(Card $card)
    {
          $voucher = new Voucher();

          $voucher->setCard($card);
          

          $serialNumber =  time() . mt_rand( 10000, 99999); 

          $voucher->setSerial($serialNumber);

          $voucher->setExpiredAt(new \DateTime( '6 months' ));

          // add voucher to Card
          $card->addVoucher($voucher);  

          $em = $this->getDoctrine()->getManager();
          $em->persist($voucher);
          $em->flush();

          $this->addFlash("success", "le bon de fideleté est generer avec succee ");

          return $this->redirectToRoute('dashboard') ;
       
    }

    /**
     * @Route("/vouchers/display/{id}", name="vouchers")
     */
    public function diplayAllvouchers(Card $card)
    {
        $vouchers = $card->getVouchers();

        return $this->render('dashbord',[

            'vouchers' =>$vouchers
        ]);

    }

    /**
     * @Route("/vouchers/delete/{id}", name="delete_voucher")
     */
    public function deletevoucher(Voucher $voucher)
    {
        $card =$voucher->getCard();

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($voucher);

        //delete voucher in card 

        $card ->removeVoucher($voucher);

        $entityManager->flush();


        return $this->render('dashbord');

    }



}
