<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class GoogleCloudStorageService implements StorageServiceInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string $bucketName,
        private readonly string $projectId,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $storage = new StorageClient(['projectId' => $this->projectId]);
        $bucket = $storage->bucket($this->bucketName);

        $bucket->upload(
            fopen($file->getPathname(), 'r'),
            ['name' => $fileName, 'predefinedAcl' => 'publicRead'],
        );

        return $fileName;
    }

    public function getPublicUrl(string $filename): string
    {
        return sprintf('https://storage.googleapis.com/%s/%s', $this->bucketName, $filename);
    }
}
