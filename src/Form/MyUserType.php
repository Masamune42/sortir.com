<?php

namespace App\Form;


use App\Entity\Establishment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MyUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Pseudo'
                ])
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom'
                ])
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'Prénom'
                ])
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone'
                ])
            ->add(
                'mail',
                EmailType::class,
                [
                    'label' => 'Email'
                ])

            ->add(
                'establishment',
                EntityType::class,
                [
                    'label' => 'Ville de rattachement',
                    'placeholder' => 'Choisir une ville',
                    'class' => Establishment::class,
                    'choice_label' => 'name',
                    'required' => false
                ]


            )


            ->add(
                'enregistrer',
                SubmitType::class,
                [
                    'label' => 'Enregistrer'
                ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
