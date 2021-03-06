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

    public function createCity(Request $request)
    {
        $city = new City();

        $cityManager = $this->entityManager->getRepository(City::class);

        if ($cityManager->findOneBy(['postCode' => $request->request->get('city_post_code')]) == null){

        $city->setName($request->request->get('city_name'))
            ->setPostCode($request->request->get('city_post_code'));

        $this->entityManager->persist($city);
        $this->entityManager->flush();
        }
    }

    public function createPlace(Request $request)
    {

        $place = new Place();

        $cityManager = $this->entityManager->getRepository(City::class);
        $city= null;
        if($request->request->get('select_postCode')) {
            $city = $cityManager->findOneBy(['postCode' => $request->request->get('select_postCode')]);
        } else {
            $city = $cityManager->findOneBy(['postCode' => $request->request->get('city_post_code')]);
        }

        $place->setName($request->request->get('place_name'));
        $place->setCity($city)
            ->setStreet($request->request->get('place_street'));
        $city->addPlace($place);

        $this->entityManager->persist($place);
        $this->entityManager->persist($city);
        $this->entityManager->flush();

    }


}