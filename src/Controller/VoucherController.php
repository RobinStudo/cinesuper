<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Voucher;
use App\Repository\EventRepository;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoucherController extends AbstractController
{
    /**
     * @Route("/voucher/{id}", name="addFidelity")
     */
    public function vouchergenerate(User $user, MailerService $mailerService, Request $request, EventRepository $eventRepository )
    {
        $card = $user->getCard();

        if ($request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            //variable multiplicator
            $multiplicator = 1;
            // creation de la variable event 
            $event = $eventRepository->findByDate();
            //si + variable
            if($event){
                //nom variable et nom de la variable event en tableau + getgetMultiplicator
                $multiplicator = $event[0]->getMultiplicator();
            }
            
            $card->setFidelity($card->getFidelity() + $request->request->get('ticketNumber') * $multiplicator);
            $em->flush();
            
            return $this->redirectToRoute('easyadmin');
        }

        return $this->render('voucher/addFidelity.html.twig', [
            'card' => $card,
        ]);
    }
}
