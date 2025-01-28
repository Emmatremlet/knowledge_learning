<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Certification;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * LessonController
 * Gère les fonctionnalités liées aux leçons.
 */
class LessonController extends AbstractController
{
    /**
     * Affiche la liste des leçons et permet d'en ajouter une nouvelle.
     *
     * @Route("/dashboard/lesson", name="admin_lesson")
     * @param LessonRepository $lessonRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/dashboard/lesson", name:"admin_lesson")]
    public function new(LessonRepository $lessonRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $lessons = $lessonRepository->findAll();
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'Leçon ajoutée avec succès !');

            return $this->redirectToRoute('admin_lesson');
        }

        return $this->render('administrator/contents/dashboard_lesson.html.twig', [
            'form' => $form->createView(),
            'lessons' => $lessons
        ]);
    }

    /**
     * Permet de modifier une leçon existante.
     *
     * @Route("/lesson/edit/{id}", name="lesson_edit")
     * @param Lesson $lesson
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/lesson/edit/{id}", name:"lesson_edit")]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Leçon modifiée avec succès !');

            return $this->redirectToRoute('admin_lesson');
        }

        return $this->render('administrator/contents/edit_lesson.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    /**
     * Supprime une leçon.
     *
     * @Route("/lesson/delete/{id}", name="lesson_delete")
     * @param Lesson $lesson
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    #[Route("/lesson/delete/{id}", name:"lesson_delete")]
    public function delete(Lesson $lesson, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($lesson);
        try {
            $entityManager->flush();
        } catch (\Doctrine\ORM\ORMException $e) {
            if (method_exists($e, 'getCycle')) {
                $cycle = $e->getCycle();
                dump($cycle);
            }
            throw $e;
        }

        $this->addFlash('success', 'Leçon supprimée avec succès !');
        return $this->redirectToRoute('admin_lesson');
    }

    /**
     * Affiche les détails d'une leçon.
     *
     * @Route("/lesson/{id}", name="lesson_detail")
     * @param Lesson $lesson
     * @return Response
     */
    #[Route("/lesson/{id}", name:"lesson_detail")]
    public function lessonDetail(Lesson $lesson): Response
    {
        $user = $this->getUser();

        $hasAccessToLesson = $user->getPurchases()->exists(function ($key, $purchase) use ($lesson) {
            return $purchase->getLesson() === $lesson;
        });

        $hasAccessToCursus = $user->getPurchases()->exists(function ($key, $purchase) use ($lesson) {
            return $purchase->getCursus() && $purchase->getCursus()->getLessons()->contains($lesson);
        });

        if (!$hasAccessToLesson && !$hasAccessToCursus) {
            $this->addFlash('danger', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('cart_index');
        }

        return $this->render('lesson/detail.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Valide une leçon et attribue une certification si toutes les leçons d'un cursus sont validées.
     *
     * @Route("/lesson/{id}/validate", name="lesson_validate", methods={"POST"})
     * @param Lesson $lesson
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/lesson/{id}/validate", name:"lesson_validate", methods:"POST")]
    public function validateLesson(Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $hasAccess = $user->getPurchases()->exists(function ($key, $purchase) use ($lesson) {
            return $purchase->getLesson() === $lesson || 
                ($purchase->getCursus() && $purchase->getCursus()->getLessons()->contains($lesson));
        });

        if (!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('cart_index');
        }

        $lesson->setIsValidated(true);
        $entityManager->flush();

        $cursus = $lesson->getCursus();
        if ($cursus && $cursus->getLessons()->forAll(fn($key, $lesson) => $lesson->isValidated())) {
            $certification = new Certification();
            $certification->setUser($user)
                ->setCursus($cursus)
                ->setIsValidated(true)
                ->setValidatedAt(new \DateTimeImmutable());
            $entityManager->persist($certification);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Leçon validée avec succès !');
        return $this->redirectToRoute('lesson_detail', ['id' => $lesson->getId()]);
    }
}