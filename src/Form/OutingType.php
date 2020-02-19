<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Group;
use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\User;
use App\Repository\GroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class OutingType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
            ->add(
                'duration',
                NumberType::class,
                ['label' => 'Durée de la sortie (en minute)']
            )
            ->add(
                'limitDateTime',
                DateTimeType::class,
                [
                    'label' => 'Date limite d\'inscription',
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'registerMax',
                NumberType::class,
                ['label' => 'Nombre de personnes maximum']
            )
            ->add(
                'infoOuting',
                TextareaType::class,
                [
                    'label' => 'Description de la sortie',
                    'required' => true,
                ]
            )
            //->add('status')
            //->add('establishment')
            ->add(
                'place',
                EntityType::class,
                [
                    'class' => Place::class,
                    'choice_label' => function (Place $place) {
                        return $place->getName()." - ".$place->getCity()->getPostCode()." ".$place->getCity()->getName(
                            );
                    },
                    'label' => 'Lieu de la sortie',
                ]
            )
            //not compulsory: related private group
            ->add(
                'usersGroup',
                EntityType::class,
                [
                    'class' => Group::class,
                    'query_builder' => function (GroupRepository $er) {
                        return $er->createQueryBuilder('g')
                            ->join('g.participants', 'p')
                            ->where("p = :user")
                            ->setParameter('user', $this->security->getUser());
                    },
                    'choice_label' => function (Group $group) {
                        return 'LIMITÉ À : '.$group->getName();
                    },
                    'required' => false,
                    'empty_data' => null,
                    'label'=>'Ouvert à',
                    'placeholder'=>'OUVERT A TOUS LES UTILISATEURS',
                ]
            )
            // A créer plus tard en gérant la sélection de la ville et ensuite du lieu
//            ->add(
//                'city',
//                EntityType::class,
//                [
//                    'mapped' => false,
//                    'class' => City::class,
//                    'choice_label' => function (City $city) {
//                        return $city->getPostCode() . " " . $city->getName();
//                    },
//                    'required' => false
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
                    'label' => 'Publier',
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
