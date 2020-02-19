<?php

namespace App\Form;

use App\Entity\Establishment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingHomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'establishment',
                EntityType::class,
                [
                    'class' => Establishment::class,
                    'choice_label' => 'name',
                    'label' => 'Site : ',
                    'required' => false,
                    'empty_data' => null,
                    'placeholder'=>'TOUS',
                ]
            )
            ->add(
                'nameContent',
                TextType::class,
                [
                    'label' => 'Le nom de la sortie contient',
                    'required'=>false,
                ]
            )
            ->add(
                'dateMin',
                DateType::class,
                [
                    'label' => 'Entre ',
                    'widget' => 'single_text',
                    'required'=>false,
                ]
            )
            ->add(
                'dateMax',
                DateType::class,
                [
                    'label' => 'Et ',
                    'widget' => 'single_text',
                    'required'=>false,
                ]
            )
            ->add(
                'iAmOrganizer',
                CheckboxType::class,
                [
                    'label'=>'Sorties dont je suis l\'organisateur/trice',
                    'required'=>false,
                ]
            )
            ->add(
                'iAmRegistred',
                CheckboxType::class,
                [
                    'label'=>'Sorties auxquelles je suis inscrit/e',
                    'required'=>false,
                ]
            )
            ->add(
                'iAmNotRegistred',
                CheckboxType::class,
                [
                    'label'=>'Sorties auxquelles je ne suis pas inscrit/e',
                    'required'=>false,
                ]
            )
            ->add(
                'passedOuting',
                CheckboxType::class,
                [
                    'label'=>'Sorties passées',
                    'required'=>false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Rechercher',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                // Configure your form options here
            ]
        );
    }
}
