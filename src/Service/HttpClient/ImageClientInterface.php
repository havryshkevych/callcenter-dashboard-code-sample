<?php declare(strict_types=1);

namespace App\Service\HttpClient;


interface ImageClientInterface
{
    public function saveImage(string $base64Image): string;
}
