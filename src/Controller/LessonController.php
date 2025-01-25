<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LessonController extends AbstractController
{

    #[Route('/dashboard/lesson', name: 'admin_lesson')]
    public function new(LessonRepository $lessonRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $lessons = $lessonRepository->findAll();
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'Leçon ajouté avec succès !');

            return $this->redirectToRoute('admin_lesson');
        }

        return $this->render('administrator/contents/dashboard_lesson.html.twig', [
            'form' => $form->createView(),
            'lessons' => $lessons
        ]);
    }
        
    #[Route('/lesson/edit/{id}', name: 'lesson_edit')]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Leçon modifié avec succès !');

            return $this->redirectToRoute('admin_lesson');
        }

        return $this->render('administrator/contents/edit_lesson.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lesson,
        ]);
    }

    #[Route('/lesson/delete/{id}', name: 'lesson_delete')]
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

        $this->addFlash('success', 'Lesson supprimé avec succès !');
        return $this->redirectToRoute('admin_lesson');
    }

    #[Route('/lesson/{id}', name: 'lesson_detail')]
    public function lessonDetail(Lesson $lesson): Response
    {
        $user = $this->getUser();

        $hasAccess = $user->getPurchases()->exists(function ($key, $purchase) use ($lesson) {
            return $purchase->getLesson() === $lesson;
        });

        if (!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('cart_index');
        }

        return $this->render('lesson/detail.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}

?>