<?php declare(strict_types=1);

namespace App\Event\Listener;

use App\Entity\User;
use App\Service\HttpClient\ImageClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UserImageListener
{
    public function __construct(
        private ImageClientInterface $imageClient,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function prePersist(User $user): void
    {
        $this->processPhoto($user);
    }

    public function preUpdate(User $user): void
    {
        $this->processPhoto($user);
    }

    private function processPhoto(User $user)
    {
        try {
            if ($user->getPhoto() && $this->isBase64($user->getPhoto()) && is_array(getimagesize($user->getPhoto()))) {
                $user->setPhoto($this->imageClient->saveImage($user->getPhoto()));
            }
        } catch (Exception $e) {
            //ignore
        }
    }

    private function isBase64(?string $string): bool
    {
        return (bool) preg_match('/^(?:[data]{4}:(text|image|application)\/[a-z]*)/', $string);
    }
}
