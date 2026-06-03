<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class LocalStorageService implements StorageServiceInterface
{
    public function __construct(private SluggerInterface $slugger) {}

    /** @throws FileException */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $file->move('Upload', $fileName);

        return $fileName;
    }

    public function getPublicUrl(string $filename): string
    {
        return 'Upload/' . $filename;
    }
}
