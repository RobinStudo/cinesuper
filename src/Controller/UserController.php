<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        if(! $user->getEnabled()){
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
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('user/dashboard.html.twig');
    }
}
