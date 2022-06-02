<?php declare(strict_types=1);

namespace App\Security;

use App\Service\HttpClient\UCBClientInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UcbUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(protected UCBClientInterface $ucbClient)
    {
    }

    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        $user = $this->ucbClient->getUser($identifier);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $user = $this->ucbClient->getUser($user->getUserIdentifier());

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        // not used
    }
}
