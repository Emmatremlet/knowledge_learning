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

/**
 * ThemeController
 * Gère les thèmes dans l'application.
 */
class ThemeController extends AbstractController
{
    /**
     * Affiche la liste des thèmes et permet d'en ajouter un nouveau.
     *
     * @Route("/dashboard/theme", name="admin_theme")
     * @param ThemeRepository $themeRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/dashboard/theme", name:"admin_theme")]
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

        return $this->render('administrator/contents/dashboard_theme.html.twig', [
            'form' => $form->createView(),
            'themes' => $themes
        ]);
    }
        
    /**
     * Permet de modifier un thème existant.
     *
     * @Route("/theme/edit/{id}", name="theme_edit")
     * @param Theme $theme
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/theme/edit/{id}", name:"theme_edit")]
    public function edit(Theme $theme, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Thème modifié avec succès !');

            return $this->redirectToRoute('admin_theme');
        }

        return $this->render('administrator/contents/edit_theme.html.twig', [
            'form' => $form->createView(),
            'theme' => $theme,
        ]);
    }

    /**
     * Supprime un thème.
     *
     * @Route("/theme/delete/{id}", name="theme_delete")
     * @param Theme $theme
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    #[Route("/theme/delete/{id}", name:"theme_delete")]
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
    
    /**
     * Affiche les détails d'un thème.
     *
     * @Route("/theme/{id}", name="theme")
     * @param Theme $theme
     * @return Response
     */
    #[Route("/theme/{id}", name:"theme")]
    public function show(Theme $theme): Response
    {
        $cursuses = $theme->getCursuses();

        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
            'cursuses' => $cursuses,
        ]);
    }

    /**
     * Affiche la liste de tous les thèmes.
     *
     * @Route("/theme", name="themes")
     * @param ThemeRepository $themeRepository
     * @return Response
     */
    #[Route("/theme", name:"themes")]
    public function list(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->getAll();

        return $this->render('theme/list.html.twig', [
            'themes' => $themes,
        ]);
    }
}

?>