<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ThemeRepository;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'themes' => $themes]);
    }
}

?>