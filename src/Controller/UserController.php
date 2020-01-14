<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Entity\ResetPassword;
use App\Service\MailerService;
use App\Form\ResetPasswordType;
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
}
