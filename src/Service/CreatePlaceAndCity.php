<?php


namespace App\Service;


use App\Entity\City;
use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CreatePlaceAndCity
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function createPlace(Request $request)
    {

        $place = new Place();
        dump('Je créé la place');
        $cityManager= $this->entityManager->getRepository(City::class);
        $place->setName($request->request->get('place_name'));
        $city = $cityManager->findOneBy(['postCode' => $request->request->get('select_postCode')]);
        dump($city);
        $place->setCity($city->getId());
        $place->setStreet($request->request->get('place_street'));


        $this->entityManager->persist($place);
        $this->entityManager->flush();
        dump('la place est créée');
    }

    public function createCity(Request $request)
    {
        $city = new City();
        dump('Je créé la ville');
        $city->setName($request->request->get('city_name'));
        $city->setPostCode($request->request->get('city_post_code'));
        dump($city);
        $this->entityManager->persist($city);
        $this->entityManager->flush();

    }
}