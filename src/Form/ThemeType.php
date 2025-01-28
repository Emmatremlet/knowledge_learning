<?php

namespace App\Form;

use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ThemeType
 *
 * Cette classe représente le formulaire pour l'entité Theme.
 * Elle permet de gérer les champs nécessaires à la création ou la modification d'un thème.
 *
 * @package App\Form
 */
class ThemeType extends AbstractType
{
    /**
     * Construit le formulaire pour l'entité Theme.
     *
     * Cette méthode ajoute des champs pour le nom et la description du thème.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du thème', 
            ])
            ->add('description') 
        ;
    }

    /**
     * Configure les options par défaut pour ce formulaire.
     *
     * Cette méthode associe le formulaire à l'entité Theme pour permettre la manipulation des données.
     *
     * @param OptionsResolver $resolver Le configurateur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class, 
        ]);
    }
}