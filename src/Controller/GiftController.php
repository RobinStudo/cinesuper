<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Gift;
use App\Entity\GiftCard;
use App\Repository\GiftRepository;
use App\Service\MailerService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GiftController extends AbstractController
{
    /**
     * 
     * Permet d'afficher le catalogue des cadeaux
     * 
     * @Route("/gift", name="gift")
     */
    public function index(GiftRepository $giftRepository)
    {
        $gifts = $giftRepository->findAll();

        return $this->render('gift/index.html.twig', [
            'gifts' => $gifts,
        ]);
    }

    /**
     * 
     * Permet d'afficher le catalogue des cadeaux
     * 
     * @Route("/buy/{id}", name="buy")
     */
    public function buy(Gift $gift, MailerService $mailer)
    {
        if ($this->getUser())
        {
            $user = $this->getUser();
            $card = $user->getCard();

            // On vérifie que l'utilisateur dispose bien des crédits suffisants
            if ($card->getFidelity() >= $gift->getNbFidelity())
            {
                $manager = $this->getDoctrine()->getManager();

                $card->setFidelity($card->getFidelity() - $gift->getNbFidelity());

                $giftCard = new GiftCard;
                $giftCard->setSerial(str_replace(' ','',$user->getFullname()).uniqid());
                $giftCard->setExpiredAt(new \DateTime('6 months'));
                $giftCard->setCards($card);
                $giftCard->setUsed(false);
                $giftCard->setGifts($gift);
                $manager->persist($giftCard);
                
                $manager->flush();

                $this->addFlash('success', 'Vous avez bien reçu votre cadeau, un mail de confirmation vous a été envoyé !');

                $mailer->vouchergenerate($user->getEmail(), $gift->getTitle());
            }
            else
            {
                $this->addFlash('danger', 'Attention, vos crédits ne sont pas suffisant !!!');
            }
            return $this->redirectToRoute('gift');
        }
        return $this->redirectToRoute('home');
    }
}
