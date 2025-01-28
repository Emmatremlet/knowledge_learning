<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Form\CursusType;
use App\Repository\CursusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * CursusController
 * Gère les fonctionnalités liées aux cursus.
 */
class CursusController extends AbstractController
{
    /**
     * Affiche la liste des cursus et permet d'en ajouter un nouveau.
     *
     * @Route("/dashboard/cursus", name="admin_cursus")
     * @param CursusRepository $cursusRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(CursusRepository $cursusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursuses = $cursusRepository->findAll();
        $cursus = new Cursus();
        $form = $this->createForm(CursusType::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursus);
            $entityManager->flush();

            $this->addFlash('success', 'Cursus ajouté avec succès !');

            return $this->redirectToRoute('admin_cursus');
        }

        return $this->render('administrator/contents/dashboard_cursus.html.twig', [
            'form' => $form->createView(),
            'cursuses' => $cursuses
        ]);
    }

    /**
     * Permet de modifier un cursus existant.
     *
     * @Route("/cursus/edit/{id}", name="cursus_edit")
     * @param Cursus $cursus
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Cursus $cursus, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursusType::class, $cursus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Cursus modifié avec succès !');

            return $this->redirectToRoute('admin_cursus');
        }

        return $this->render('administrator/contents/edit_cursus.html.twig', [
            'form' => $form->createView(),
            'cursus' => $cursus,
        ]);
    }

    /**
     * Supprime un cursus.
     *
     * @Route("/cursus/delete/{id}", name="cursus_delete")
     * @param Cursus $cursus
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete(Cursus $cursus, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($cursus);
        $entityManager->flush();

        $this->addFlash('success', 'Cursus supprimé avec succès !');
        return $this->redirectToRoute('admin_cursus');
    }

    /**
     * Affiche les détails d'un cursus.
     *
     * @Route("/cursus/{id}", name="cursus_detail")
     * @param Cursus $cursus
     * @return Response
     */
    public function cursusDetail(Cursus $cursus): Response
    {
        $user = $this->getUser();

        $hasAccess = $user->getPurchases()->exists(function ($key, $purchase) use ($cursus) {
            return $purchase->getCursus() === $cursus;
        });

        if (!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas accès à ce cursus.');
            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cursus/detail.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}