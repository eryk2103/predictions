<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageServiceInterface
{
    public function upload(UploadedFile $file): string;

    public function getPublicUrl(string $filename): string;
}
