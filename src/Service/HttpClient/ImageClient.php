<?php declare(strict_types=1);

namespace App\Service\HttpClient;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageClient implements ImageClientInterface
{

    public function __construct(
        private string $mediaBaseUrl,
        private HttpClientInterface $httpClient
    )
    {
    }

    public function saveImage(string $base64Image): string
    {
        $response = $this->httpClient->request(
            Request::METHOD_POST,
            '/api/images/direct-upload',
            [
                'base_uri' => $this->mediaBaseUrl,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "type" => "avatar",
                    "image" => $base64Image
                ]
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new ClientException($response);
        }

        return @$response->toArray()['src'] ?: '';
    }

}
