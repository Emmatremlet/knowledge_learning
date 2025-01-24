<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ThemeController extends AbstractController
{

    #[Route('/dashboard/theme', name: 'admin_theme')]
    public function new(ThemeRepository $themeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $themes = $themeRepository->findAll();
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Thème ajouté avec succès !');

            return $this->redirectToRoute('admin_theme');
        }

        return $this->render('administrator/dashboard_theme.html.twig', [
            'form' => $form->createView(),
            'themes' => $themes
        ]);
    }
        
    #[Route('/theme/edit/{id}', name: 'theme_edit')]
    public function edit(Theme $theme, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Thème modifié avec succès !');

            return $this->redirectToRoute('admin_theme');
        }

        return $this->render('administrator/edit_theme.html.twig', [
            'form' => $form->createView(),
            'theme' => $theme,
        ]);
    }

    #[Route('/theme/delete/{id}', name: 'theme_delete')]
    public function delete(Theme $theme, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($theme);
        try {
            $entityManager->flush();
        } catch (\Doctrine\ORM\ORMException $e) {
            if (method_exists($e, 'getCycle')) {
                $cycle = $e->getCycle();
                dump($cycle);
            }
            throw $e;
        }

        $this->addFlash('success', 'Thème supprimé avec succès !');
        return $this->redirectToRoute('admin_theme');
    }
    
    #[Route('/theme/{id}', name: 'theme')]
    public function show(Theme $theme): Response
    {
        // Récupérer tous les cursus et leurs leçons associés au thème
        $cursuses = $theme->getCursuses();

        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
            'cursuses' => $cursuses,
        ]);
    }

    #[Route('/theme', name: 'themes')]
    public function list(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->getAll();

        return $this->render('theme/list.html.twig', [
            'themes' => $themes,
        ]);
    }
}

?>