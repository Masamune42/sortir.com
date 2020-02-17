<?php

namespace App\Form;

use App\Entity\Establishment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Pseudo',
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'label' => "Conditions d'utilisations",
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'Veuillez accepter nos termes',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'Prénom',
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                ]
            )
            ->add(
                'mail',
                EmailType::class,
                [
                    'label' => 'Email',
                ]
            )
            ->add(
                'establishment',
                EntityType::class,
                [
                    'label' => 'Ville de rattachement',
                    'placeholder' => 'Choisir une ville',
                    'class' => Establishment::class,
                    'choice_label' => 'name',
                    'required' => false,
                ]


            )
            ->add(
                'password',
                PasswordType::class,
                [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'label'=> 'Mot de passe',
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Veuillez entrer un mot de passe',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Le mot de passe ne peux pas avoir moins de  {{ limit }} charactères',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]
                        ),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
