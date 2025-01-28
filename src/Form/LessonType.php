<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Lesson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

/**
 * Class LessonType
 *
 * Cette classe représente le formulaire pour l'entité Lesson.
 * Elle permet de gérer les champs nécessaires à la création ou la modification d'une leçon.
 *
 * @package App\Form
 */
class LessonType extends AbstractType
{
    /**
     * Construit le formulaire pour l'entité Lesson.
     *
     * Cette méthode ajoute des champs spécifiques à l'entité, incluant le nom, la description,
     * l'URL de la vidéo, le prix et le cursus auquel la leçon appartient.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la leçon',
            ])
            ->add('description')
            ->add('videoUrl')
            ->add('price', MoneyType::class, [
                'label' => 'Prix en ',
            ])
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'id',
                'label' => 'Cursus associé',
            ]);
    }

    /**
     * Configure les options par défaut pour ce formulaire.
     *
     * Cette méthode associe le formulaire à l'entité Lesson pour permettre la manipulation des données.
     *
     * @param OptionsResolver $resolver Le configurateur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}