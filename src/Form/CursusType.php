<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Theme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

/**
 * Class CursusType
 *
 * Cette classe représente le formulaire pour l'entité Cursus.
 * Elle permet de gérer les champs nécessaires à la création ou la modification d'un cursus.
 *
 * @package App\Form
 */
class CursusType extends AbstractType
{
    /**
     * Construit le formulaire pour l'entité Cursus.
     *
     * Cette méthode ajoute des champs spécifiques à l'entité, incluant le nom, la description,
     * le prix et le thème du cursus. Le champ thème est lié à l'entité Theme via une relation Doctrine.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du cursus',
            ])
            ->add('description')
            ->add('price', MoneyType::class, [
                'label' => 'Prix en ',
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'id',
                'label' => 'Thème du cursus',
            ]);
    }

    /**
     * Configure les options par défaut pour ce formulaire.
     *
     * Cette méthode associe le formulaire à l'entité Cursus pour permettre la manipulation des données.
     *
     * @param OptionsResolver $resolver Le configurateur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cursus::class,
        ]);
    }
}