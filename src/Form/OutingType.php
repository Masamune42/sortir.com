<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Outing;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['label' => 'Nom de la sortie']
            )
            ->add(
                'startTime',
                DateTimeType::class,
                [
                    'label' => 'Date de début de sortie',
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add('duration')
            ->add(
                'limitDateTime',
                DateTimeType::class,
                [
                    'label' => 'Date de sortie',
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add('registerMax')
            ->add('infoOuting')
            //->add('status')
            //->add('establishment')
            ->add(
                'place',
                EntityType::class,
                [
                    'class' => Place::class,
                    'choice_label' => 'name',
                    'label' => 'Place'
                ]
            )
//            ->add(
//                'city',
//                EntityType::class,
//                [
//                    'mapped' => false,
//                    'class' => City::class,
//                    'choice_label' => 'name',
//                ]
//            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Enregistrer',
                ]
            )->add(
                'createOuting',
                SubmitType::class,
                [
                    'label' => 'Créer une sortie',
                ]
            );
        //->add('organizer')
        //->add('participant');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Outing::class,
            ]
        );
    }
}
