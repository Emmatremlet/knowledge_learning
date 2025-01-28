<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * CertificationController
 * Gère les fonctionnalités liées aux certifications des utilisateurs.
 */
class CertificationController extends AbstractController
{
    /**
     * Affiche la liste des certifications de l'utilisateur connecté.
     *
     * @Route("/certifications", name="user_certifications")
     * @return Response
     */
    public function list(): Response
    {
        $user = $this->getUser();

        return $this->render('certification/list.html.twig', [
            'certifications' => $user->getCertifications(),
        ]);
    }
}