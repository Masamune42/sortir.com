<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Establishment;
use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class OutingFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $statut1 = new Status();
        $statut1->setName("Brouillon")
            ->setNameTech("draft");
        $manager->persist($statut1);
        $statut2 = new Status();
        $statut2->setName("Publiée")
            ->setNameTech("published");
        $manager->persist($statut2);
        $statut3 = new Status();
        $statut3->setName("Annulée")
            ->setNameTech("canceled");
        $manager->persist($statut3);

        $establishment1 = new Establishment();
        $establishment1->setName("Campus de Rennes");
        $manager->persist($establishment1);
        $establishment2 = new Establishment();
        $establishment2->setName("Campus de Quimper");
        $manager->persist($establishment2);
        $establishment3 = new Establishment();
        $establishment3->setName("Campus de Faraday");
        $manager->persist($establishment3);
        $establishment4 = new Establishment();
        $establishment4->setName("Campus de Niort");
        $manager->persist($establishment4);

        $city1 = new City();
        $city1->setName("Saint-Herblain")
            ->setPostCode("44800");
        $manager->persist($city1);
        $city2 = new City();
        $city2->setName("Rennes")
            ->setPostCode("35000");
        $manager->persist($city2);
        $city3 = new City();
        $city3->setName("Quimper")
            ->setPostCode("29000");
        $manager->persist($city3);
        $city4 = new City();
        $city4->setName("Niort")
            ->setPostCode("79000");
        $manager->persist($city4);
        $city5 = new City();
        $city5->setName("Paris")
            ->setPostCode("75000");
        $manager->persist($city5);
        $city6 = new City();
        $city6->setName("Vannes")
            ->setPostCode("56000");
        $manager->persist($city6);

        $user1 = new User();
        $password1 = 'aclaich';
        $encodedPassword1 = $this->encoder->encodePassword($user1, $password1);
        $user1->setUsername("aclaich")
            ->setPassword($encodedPassword1)
            ->setFirstname("Alexis")
            ->setName("Claich")
            ->setPhone("0666422536")
            ->setMail("aclaich@mail.fr")
            ->setAdministrator(false)
            ->setActive(true)
            ->setEstablishment($establishment2);
        $manager->persist($user1);

        $user2 = new User();
        $password2 = 'rturpin';
        $encodedPassword2 = $this->encoder->encodePassword($user2, $password2);
        $user2->setUsername("rturpin")
            ->setPassword($encodedPassword2)
            ->setFirstname("Ronan")
            ->setName("Turpin")
            ->setPhone("0666526536")
            ->setMail("rturpin@mail.fr")
            ->setAdministrator(false)
            ->setActive(true)
            ->setEstablishment($establishment1);
        $manager->persist($user2);

        $user3 = new User();
        $password3 = 'vmedonne';
        $encodedPassword3 = $this->encoder->encodePassword($user3, $password3);
        $user3->setUsername("vmedonne")
            ->setPassword($encodedPassword3)
            ->setFirstname("Vianney")
            ->setName("Medonne")
            ->setPhone("0666558936")
            ->setMail("vmedonne@mail.fr")
            ->setAdministrator(false)
            ->setActive(true)
            ->setEstablishment($establishment3);
        $manager->persist($user3);

        $user4 = new User();
        $password4 = 'acaignard';
        $encodedPassword4 = $this->encoder->encodePassword($user4, $password4);
        $user4->setUsername("acaignard")
            ->setPassword($encodedPassword4)
            ->setFirstname("Alexandre")
            ->setName("Caignard")
            ->setPhone("0625498236")
            ->setMail("acaignard@mail.fr")
            ->setAdministrator(false)
            ->setActive(true)
            ->setEstablishment($establishment2);
        $manager->persist($user4);

        $user5 = new User();
        $password5 = 'jesuisleboss';
        $encodedPassword5 = $this->encoder->encodePassword($user5, $password5);
        $user5->setUsername("admin1")
            ->setPassword($encodedPassword5)
            ->setFirstname("Edit")
            ->setName("Piaf")
            ->setPhone("0625846725")
            ->setMail("edit.piaf@died.fr")
            ->setAdministrator(true)
            ->setActive(true)
            ->setEstablishment($establishment4);
        $manager->persist($user5);

        $user6 = new User();
        $password6 = 'utilisateur_supprimé';
        $encodedPassword6 = $this->encoder->encodePassword($user6, $password6);
        $user6->setUsername("utilisateur_supprimé")
            ->setPassword($encodedPassword6)
            ->setFirstname("NA")
            ->setName("NA")
            ->setPhone("0000000000")
            ->setMail("NA@NA.NA")
            ->setAdministrator(false)
            ->setActive(false)
            ->setEstablishment($establishment4);
        $manager->persist($user6);


        // 47.2258726,-1.6265994
        $place1 = new Place();
        $place1->setName("Saveurs d'Asie")
            ->setCity($city1)
            ->setStreet("Place Océane")
            ->setLatitude(47.2258726)
            ->setLongitude(-1.6265994);
        $manager->persist($place1);

        //48.113806,-1.6831262
        $place2 = new Place();
        $place2->setName("POUTINEBROS RENNES")
            ->setCity($city2)
            ->setStreet("17-19 Rue de Penhoët")
            ->setLatitude(48.113806)
            ->setLongitude(-1.6831262);
        $manager->persist($place2);

        //47.9755147,-4.098268
        $place3 = new Place();
        $place3->setName("Ayako Sushi")
            ->setCity($city3)
            ->setStreet("163 Route de Bénodet")
            ->setLatitude(47.9755147)
            ->setLongitude(-4.098268);
        $manager->persist($place3);

        //46.3252867,-0.4652037
        $place4 = new Place();
        $place4->setName("Musée du Donjon")
            ->setCity($city4)
            ->setStreet("Rue du Guesclin")
            ->setLatitude(46.3252867)
            ->setLongitude(-0.4652037);
        $manager->persist($place4);

        //48.8533971,2.3431144
        $place5 = new Place();
        $place5->setName("Shiso Burger")
            ->setCity($city5)
            ->setStreet("21 Quai Saint-Michel")
            ->setLatitude(48.8533971)
            ->setLongitude(2.3431144);
        $manager->persist($place5);

        for ($i = 0; $i < 20; $i++) {
            $outing1 = new Outing();
            $outing1->setName("Sortie resto asiat Atlantis (n°".$i.")")
                ->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-27 19:30:00'))
                ->setDuration(90)
                ->setLimitDateTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-24 15:30:00'))
                ->setRegisterMax(10)
                ->setInfoOuting(
                    "On se fait un petit resto asiat à volonté, si vous aimez la bonne bouffe asiat venez nombreux !"
                )
                ->setOrganizer($user2)
                ->setEstablishment($establishment1)
                ->setPlace($place1)
                ->setStatus($statut2)
                ->addParticipant($user3)
                ->addParticipant($user2);
            $manager->persist($outing1);
        }

        $outing2 = new Outing();
        $outing2->setName("Petite Poutine à Rennes au calme")
            ->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-03-01 20:00:00'))
            ->setDuration(90)
            ->setLimitDateTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-28 18:00:00'))
            ->setRegisterMax(3)
            ->setInfoOuting(
                "Pour ceux qui ne connaissent pas le saveur d'une bonne poutine, je vous propose une sortie à ce fameux restaurant de Rennes. Vous ne serez pas déçus !"
            )
            ->setOrganizer($user1)
            ->setEstablishment($establishment2)
            ->setPlace($place2)
            ->setStatus($statut2)
            ->addParticipant($user4)
            ->addParticipant($user2);
        $manager->persist($outing2);

        $outing3 = new Outing();
        $outing3->setName("Shiso Burger Saint-Michel")
            ->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-15 20:00:00'))
            ->setDuration(90)
            ->setLimitDateTime(\DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-13 18:00:00'))
            ->setRegisterMax(3)
            ->setInfoOuting(
                "J'organise une sortie à Saint-Michel pour se manger un Shiso Burger =) Places limitées !"
            )
            ->setOrganizer($user4)
            ->setEstablishment($establishment2)
            ->setPlace($place5)
            ->setStatus($statut2)
            ->addParticipant($user4)
            ->addParticipant($user2)
            ->addParticipant($user3);
        $manager->persist($outing3);

        $manager->flush();
    }
}
