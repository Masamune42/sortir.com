<?php


namespace App\Service;


use App\Entity\City;
use App\Entity\Place;
use Symfony\Component\HttpFoundation\Request;

class CreatePlaceAndCity
{

    public function createPlace(Request $request)
    {
        $place = new Place();
    }

    public function createCity(Request $request)
    {
        $city = new City();
    }
}