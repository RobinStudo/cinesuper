<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/fidelityup/{id}", name="addFidelity")
     * @param User $user
     * @param MailerService $mailerService
     * @param Request $request
     * @return Response
     */
    public function fidelityUp(User $user, MailerService $mailerService, Request $request)
    {
        $card = $user->getCard();

        if ($request->isMethod('POST')) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $card->setFidelity($card->getFidelity() + $request->request->get('ticketNumber'));

            $em->flush();
        }

        return $this->render('fidelity/addFidelity.html.twig', [
            'card' => $card,
        ]);
    }

    public function createEvent() {
        
    }
}
