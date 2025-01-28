<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * SecurityController
 * Gère la connexion et la déconnexion des utilisateurs.
 */
class SecurityController extends AbstractController
{
    /**
     * Permet aux utilisateurs de se connecter.
     *
     * @Route(path: "/login", name: "app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route("/login", name:"app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Permet aux utilisateurs de se déconnecter.
     *
     * @Route(path: "/logout", name: "app_logout", methods={"GET"})
     */
    #[Route("/logout", name:"app_logout", methods:"GET")]
    public function logout(): never
    {
    }
}