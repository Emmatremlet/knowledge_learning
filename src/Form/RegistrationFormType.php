<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType
 *
 * Cette classe représente le formulaire d'inscription pour l'entité User.
 * Elle gère les champs nécessaires à la création d'un utilisateur, notamment le nom,
 * l'email, l'acceptation des termes et le mot de passe.
 *
 * @package App\Form
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Construit le formulaire d'inscription pour l'entité User.
     *
     * Cette méthode ajoute des champs pour le nom, l'email, l'acceptation des termes,
     * et le mot de passe en texte brut (non mappé directement à l'entité).
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name') 
            ->add('email') 
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Acceptez-vous les termes ?',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez être d\'accord avec les termes.', 
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096, 
                    ]),
                ],
            ]);
    }

    /**
     * Configure les options par défaut pour ce formulaire.
     *
     * Cette méthode associe le formulaire à l'entité User pour permettre la manipulation des données utilisateur.
     *
     * @param OptionsResolver $resolver Le configurateur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}