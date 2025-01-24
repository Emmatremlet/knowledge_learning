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

class CursusController extends AbstractController
{

    #[Route('/dashboard/cursus', name: 'admin_cursus')]
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
        
    #[Route('/cursus/edit/{id}', name: 'cursus_edit')]
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

    #[Route('/cursus/delete/{id}', name: 'cursus_delete')]
    public function delete(Cursus $cursus, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($cursus);
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
        return $this->redirectToRoute('admin_cursus');
    }
}

?>