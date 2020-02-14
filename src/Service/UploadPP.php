<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadPP
{
    private $fichier;

    public function __construct($fichier)
    {
        $this->fichier = $fichier;
    }

    public function upload(UploadedFile $picture)
    {
        $fileName = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $fileName.'-'.uniqid().'-'.$picture->guessExtension();

        try {
            $picture->move($this->getTargetDirectory(), $newFileName);

        } catch(FileException $e) {

        }
            return $newFileName;
    }

    public function getTargetDirectory()
    {
        return $this->fichier;
    }

}
