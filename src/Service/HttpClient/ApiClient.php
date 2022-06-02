<?php declare(strict_types=1);

namespace App\Service\HttpClient;

use App\Service\Cache\CacheInterface;
use App\Service\Monolog\ApiLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient implements ApiClientInterface
{
    const STOPWATCH_KEY = 'api-request';

    protected LoggerInterface $logger;
    protected SerializerInterface $serializer;

    public function __construct(
        protected HttpClientInterface $httpClient,
        protected Stopwatch $stopwatch,
        protected CacheInterface $cache
    ) {
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * @param string $method
     * @param string $url
     * @param string[] $headers
     * @param array $query
     * @param array $content
     *
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function request(
        string $method,
        string $url,
        array $headers = [],
        array $query = [],
        array $content = []
    ): string {
        $this->stopwatch->start(self::STOPWATCH_KEY);

        $response = $this->httpClient->request(
            $method,
            $url,
            ['headers' => $headers, 'query' => $query, 'json' => $content]
        );
        $httpRequest = Request::create($response->getInfo()['url'] ?? $url, $method);
        $httpResponse = new Response(
            $response->getContent(false),
            $response->getStatusCode(),
            $response->getHeaders(false)
        );
        $event = $this->stopwatch->stop(self::STOPWATCH_KEY);
        ApiLogger::log($this->logger, $httpRequest, $httpResponse, $event->getDuration(), $event->getMemory());

        if (!$httpResponse->isSuccessful()) {
            throw new ClientException($response);
        }

        return $response->getContent();
    }
}
