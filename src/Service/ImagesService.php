<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class ImagesService
{
    private $kernel;
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    function saveToDisk(UploadedFile $image) {
        $path = $this->kernel->getProjectDir().'/public/gifts/';
        $imageName = uniqid() . '.' . $image->guessExtension();
        $image->move($path,$imageName);
        return $imageName;
    }
}