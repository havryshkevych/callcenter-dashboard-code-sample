<?php declare(strict_types=1);

namespace App\Service\HttpClient;

use App\Security\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class UCBClient implements UCBClientInterface
{
    protected SerializerInterface $serializer;
    protected LoggerInterface $logger;

    public function __construct(
        protected string $baseUrl,
        protected ApiClientInterface $apiClient
    ) {
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        $this->apiClient->setLogger($logger);

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
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUser(string $token): ?User
    {
        $content = $this->apiClient->request(
            Request::METHOD_GET,
            $this->baseUrl.'/api/basic-profile',
            ['Authorization' => 'Bearer '.$token]
        );

        /** @var User $user */
        $user = $this->serializer->deserialize($content, User::class, 'json');
        if ($user) {
            $user->setToken($token);
        }

        return $user;
    }
}
