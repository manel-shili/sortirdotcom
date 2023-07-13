<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

class ImageUpload
{

    public function __construct()
    {
    }

    public function upload(UploadedFile $file, $userId){

        $fileName = $userId.'.'.$file->guessExtension();
        $file->move('assets/images/profilePhoto', $fileName);
        return $fileName;

    }

}