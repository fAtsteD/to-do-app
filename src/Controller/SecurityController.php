<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Document\User;
use App\Form\RegistrateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * All actions is about security of app
 */
class SecurityController extends AbstractController
{
    /** @var ManagerRegistry $documentManager  */
    private $documentManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->documentManager = $managerRegistry->getManager();
    }

    /**
     * Sign in users
     * 
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * 
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is logged in
        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("list_page");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Registrate user
     *
     * @param Request $request
     * @return Response
     * 
     * @Route("/signup", name="registrate")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // If user is logged in
        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("list_page");
        }

        $user = new User();
        $registrateForm = $this->createForm(RegistrateType::class, $user);

        $registrateForm->handleRequest($request);
        if ($registrateForm->isSubmitted() && $registrateForm->isValid()) {
            $user = $registrateForm->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $registrateForm->get('plainPassword')->getData()));

            $this->documentManager->persist($user);
            $this->documentManager->flush();

            return $this->redirectToRoute('list_page');
        }

        return $this->render('security/registrate.html.twig', [
            'registrateForm' => $registrateForm->createView(),
        ]);
    }

    /**
     * Log out user and redicrect for login
     *
     * @return Response
     * 
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('list_page');
        }
    }
}
