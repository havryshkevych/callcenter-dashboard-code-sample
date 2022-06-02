<?php declare(strict_types=1);

namespace App\Security;

use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Throwable;

class JwtUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(protected string $jwtPublicKey)
    {
        $this->jwtPublicKey = file_get_contents($this->jwtPublicKey);
    }

    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        if (str_starts_with(strtolower($identifier), 'basic ')) {
            return (new User())->setToken($identifier);
        }

        try {
            $jwt = JWT::decode($identifier, $this->jwtPublicKey, ['RS256']);
        } catch (Throwable) {
            throw new UserNotFoundException();
        }

        return (new User())
            ->setToken($identifier)
            ->setPhone($jwt->username ?? '')
            ->setRoles($jwt->roles ?? []);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return (new User())
            ->setToken($user->getUserIdentifier())
            ->setPhone($user->getPhone())
            ->setRoles($user->getRoles());
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
