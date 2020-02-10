<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label'=> 'Pseudo'
                ])
            ->add(
                'name',
                TextType::class,
                [
                    'label'=> 'Nom'
                ])
            ->add(
                'firstname',
                TextType::class,
                [
                    'label'=> 'Prénom'
                ])
            ->add(
                'phone',
                TextType::class,
                [
                    'label'=> 'Téléphone'
                ])
            ->add(
                'mail',
                EmailType::class,
                [
                    'label'=> 'Email'
                ])
            ->add(
                'password',
                RepeatedType::class,
                ['type' => PasswordType::class,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation'],
                    'invalid_message' => 'Les mots de passe doivent correspondre',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez mettre un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe ne peux pas avoir moins de  {{ limit }} charactères',
                            // max length allowed by Symfony for security reasons
                            'max' => 1060,])
                        ]
            ])
            ->add(
                'city',
                EntityType::class,
                [
                    'label' => 'Ville de rattachement',
                    'placeholder' => 'Choisir une ville',
                    'class' => City::class,
                    'choice_label' => 'name',
                    'required' => false
                ]

            )
            ->add(
                'enregistrer',
                SubmitType::class,
                [
                    'label'=>'Enregistrer'
                ])
            ->add(
                'annuler',
                SubmitType::class,
                [
                    'label'=>'Annuler'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
