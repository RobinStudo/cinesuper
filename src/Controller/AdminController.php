<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EventService;
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
    public function fidelityUp(User $user, EventService $eventService, MailerService $mailerService, Request $request)
    {
        $card = $user->getCard();

        if ($request->isMethod("POST")) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $newsFidelityMultiplicator = $eventService->getMultiplicatorWhenEvent();

            $earnedFidelityPoints = $request->request->get("ticketNumber") * $newsFidelityMultiplicator;

            $card->setFidelity($card->getFidelity() + $earnedFidelityPoints);

            $em->flush();
        }

        return $this->render("fidelity/addFidelity.html.twig", [
            "card" => $card,
            "newsFidelityMultiplicator" => $newsFidelityMultiplicator,
        ]);
    }
}
