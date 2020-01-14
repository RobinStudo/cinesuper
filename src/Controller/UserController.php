<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\RegisterType;
use App\Service\MailerService;
use App\Service\UserService;

class UserController extends AbstractController
{
    private $encoder;
    private $userService;
    private $mailer;

    public function __construct( UserPasswordEncoderInterface $encoder, MailerService $mailer, UserService $userService ){
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->userService = $userService;
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
     * Active account 
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
                $user->setToken(null);
                // database entry
                $em = $this->getDoctrine()->getManager();
                $em->flush($user);
                // add message if account is activate
                $this->addFlash(
                    'info',
                    'Votre compte a été activé');
                
            } else {
                // add message if date is expired
                $this->addFlash(
                    'danger',
                    'il y a eu un souci à la création de votre compte. <a href="/user/resendactivatetoken/'.$user->getId().'"> Renvoyer le mail d\'activation </a>');
            }
        }
        // redirect to login route 
        return $this->redirectToRoute('login');
    }

    /**
     * send activate token
     *
     * @Route("user/resendactivatetoken/{id}", name="user_resendactivatetoken")
     */
    public function resendactivatetoken (User $user){

        // resend a activation token 
        $this->mailer->sendActivationMail( $user );
         // redirect to login route 
        return $this->redirectToRoute('login');
    
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
        return new Response('bonjour');
    }
}
