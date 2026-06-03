<?php

namespace App\Twig;

use App\Service\StorageServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StorageExtension extends AbstractExtension
{
    public function __construct(private readonly StorageServiceInterface $storage) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('storage_url', fn(string $filename) => $this->storage->getPublicUrl($filename)),
        ];
    }
}
