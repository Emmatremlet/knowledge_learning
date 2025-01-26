<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CertificationController extends AbstractController
{
    #[Route('/certifications', name: 'user_certifications')]
    public function list(): Response
    {
        $user = $this->getUser();

        return $this->render('certification/list.html.twig', [
            'certifications' => $user->getCertifications(),
        ]);
    }
}