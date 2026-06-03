<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageServiceInterface
{
    function upload(UploadedFile $file);
}
