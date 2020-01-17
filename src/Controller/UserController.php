<?php
namespace App\Controller;

use App\Entity\Gift;
use App\Entity\GiftType;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Form\RenewPasswordType;
use App\Repository\GiftTypeRepository;
use App\Repository\UserRepository;
use App\Service\GiftService;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Entity\ResetPassword;
use App\Service\MailerService;
use App\Form\ResetPasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class UserController extends AbstractController
{
    private $encoder;
    private $userService;
    private $mailer;
    private $urlGenerator;

    public function __construct( UserPasswordEncoderInterface $encoder, MailerService $mailer, UserService $userService, UrlGeneratorInterface $urlGenerator ){
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register( Request $request ): Response
    {
        if( $this->getUser() ){
            return $this->redirectToRoute('dashboard');
        }

        $user = new User();
        $form = $this->createForm( RegisterType::class, $user );

        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ){
            $password = $this->encoder->encodePassword( $user, $user->getPassword() );
            $user->setPassword( $password );

            $this->userService->generateToken( $user );
            $card = $this->userService->generateCard( $user );

            $em = $this->getDoctrine()->getManager();
            $em->persist( $card );
            $em->persist( $user );
            $em->flush();

            $this->mailer->sendActivationMail( $user );

            $this->addFlash( 'info', 'Votre compte à bien été créé, activez le pour pouvoir vous connecter' );
            return $this->redirectToRoute( 'login' );
        }

        return $this->render('user/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if( $this->getUser() ){
            $this->addFlash('info', 'Vous êtes déjà connecté(e)');
            return $this->redirectToRoute('dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
             $this->addFlash( 'danger', $error->getMessage() );
        }

        return $this->render('user/login.html.twig', array(
            'last_username' => $lastUsername,
        ));
    }

    /**
     * Activate account
     *
     * @Route("/user/activate/{token}", name="user_activate")
     */
    public function activate( $token, User $user )
    {
        if(! $user->getEnabled())
        {
            if ($user->getTokenExpire() > new \DateTime())
            {
                // set enable true and token null if valid condition
                $user->setEnabled(true);
                $this->userService->resetToken($user);
                // database entry
                $em = $this->getDoctrine()->getManager();
                $em->flush($user);
                // add message if account is activate
                $this->addFlash(
                    'info',
                    'Votre compte a été activé');
            } else {
                // add message if date is expired
                $url = $this->urlGenerator->generate( 'user_resendactivatetoken', ['id' => $user->getId()], UrlGenerator::ABSOLUTE_URL);

                $this->addFlash(
                    'danger',
                    'Ce lien a expiré <a href="'.$url.'"> Renvoyer le mail d\'activation </a>');
            }
        }
        // redirect to login route
        return $this->redirectToRoute('login');
    }

    /**
     * Send activate token
     *
     * @Route("user/resendactivatetoken/{id}", name="user_resendactivatetoken")
     */
    public function resendactivatetoken (User $user){

        if( !$user->getEnabled() ){
            // generate token and expire date
            $this->userService->generateToken($user);
            $em = $this->getDoctrine()->getManager();
            $em->flush($user);
            // resend a activation token
            $this->mailer->sendActivationMail($user);
            // message if link is send.
            $this->addFlash(
                    'info',
                    'Un lien d\'activation vous a été envoyé');
            // redirect to login route
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}

    /**
     * @Route("/dashboard", name="dashboard", methods={"GET", "POST"})
     * @param Request $request
     * @param PictureService $pictureService
     * @param GiftTypeRepository $giftTypeRepository
     * @return Response
     */
    public function dashboard(Request $request, PictureService $pictureService, GiftTypeRepository $giftTypeRepository)
    {
        $giftTypes = $giftTypeRepository->findAll();

        $picture = new Picture();

        $avatarForm = $this->createForm(PictureType::class, $picture);

        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $user = $this->getUser();

            $em = $this
                ->getDoctrine()
                ->getManager();

            if ($user->getPicture()) {
                $pictureService->deleteLastPicture($user);
            }

            $user->setPicture($picture);

            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute("dashboard");
        }

        return $this->render("user/dashboard.html.twig", [
            "avatarForm" => $avatarForm->createView(),
            "giftTypes" => $giftTypes,
        ]);
    }

    /**
     * Permet d'initier la méthode du mot de passe oublié
     *
     * @Route("/mot-de-passe-oublie",name="forget_password")
     *
     * @param Request $request
     * @return Response
     */
    public function forgetPassword(Request $request)
    {
        if ($request->isMethod('POST'))
        {
            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user) {
                $this->userService->generateToken( $user );
                $entityManager->flush();

                $this->mailer->sendResetPassword( $user );
            }

            $this->addFlash('info', 'Si un compte existe avec cette adresse email, un email vous sera envoyé.');
            return $this->redirectToRoute('home');
        }
        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * Permet de réintialiser le mot de passe
     *
     * @Route("/reset_password/{token}", name="reset_password")
     *
     * @param string $token
     * @param Request $request
     * @return Response
     */
    public function resetPassword(string $token = "0", Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneByToken($token);

        if ($user == null) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('home');
        }

        $resetPassword = new ResetPassword;

        $form = $this->createForm(ResetPasswordType::class, $resetPassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($user->getTokenExpire() < new \DateTime('now'))
            {
                $this->addFlash('alert', 'Votre token a expiré.');
            }
            else
            {
                $user->setPassword($this->encoder->encodePassword($user, $resetPassword->getNewPassword()));
                $this->userService->resetToken($user);
                $entityManager->flush();

                $this->addFlash('success', 'Le mot de passe a bien été modifié.');
            }
            return $this->redirectToRoute('home');
        }

        return $this->render('user/reset_password.html.twig',[
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("dashboard/renewpassword", name="renew_password", methods={"GET", "POST"})
     * @param Request $request
     * @param RenewPasswordType $renewPasswordType
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function renewPassword(Request $request, RenewPasswordType $renewPasswordType, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute("home");
        }

        $form = $this->createForm(RenewPasswordType::class, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form["new_password"]->getData()["newPassword"];

            $user->setPassword($encoder->encodePassword($user, $newPassword));

            $this
                ->getDoctrine()
                ->getManager()
                ->flush();

            $this->addFlash("success", "Votre mot de passe a bien été modifié.");

            return $this->redirectToRoute("dashboard");
        }

        return $this->render("user/changePassword.html.twig", [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("dashboard/spendFidelity", name="spendFidelity", methods= {"GET"})
     * @param GiftTypeRepository $giftTypeRepository
     * @return Response
     */
    public function spendFidelity(GiftTypeRepository $giftTypeRepository)
    {
        $giftTypes = $giftTypeRepository->findAll();

        return $this->render("user/spendFidelity.html.twig", [
            "giftTypes" => $giftTypes,
        ]);
    }

    /**
     * @Route("dashboard/giveGift/{id}", name="giveGift")
     * @param GiftType $giftType
     * @param GiftService $giftService
     * @return RedirectResponse
     */
    public function giveGift(GiftType $giftType, GiftService $giftService)
    {
        $user = $this->getUser();
        $userCard = $user->getCard();

        $newFidelityPoints = ($userCard->getFidelity() - $giftType->getFidelityCost());

        if ($newFidelityPoints <= 0) {
            $this->addFlash("warning", "Vous n'avez malheureusement pas assez de points de fidélité pour ce cadeau :(.");

            return $this->redirectToRoute("dashboard");
        }

        $gift = new Gift();

        $gift->setGiftType($giftType);

        $userCard->addGift($gift);
        $userCard->setFidelity($newFidelityPoints);
        $giftService->generateSerial($user, $gift);

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->persist($gift);
        $em->flush();

        return $this->redirectToRoute("dashboard");
    }
}
