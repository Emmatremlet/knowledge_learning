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
        $themes = $themeRepository->findTopTwoThemesByTotalCursusPrice();

        $formattedThemes = array_map(function ($result) {
            return [
                'theme' => $result[0],
                'total_price' => $result['total_price'],
            ];
        }, $themes);
        return $this->render('home/index.html.twig', [
            'themes' => $formattedThemes,
        ]);
    }
}

?>