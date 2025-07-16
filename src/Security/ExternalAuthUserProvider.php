<?php

namespace App\Security;

use App\Entity\ExternalAuthUser;
use App\Service\Auth\AuthService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

readonly class ExternalAuthUserProvider implements UserProviderInterface
{
    public function __construct(
        private AuthService $authService,
    )
    {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        /** @var ExternalAuthUser $user */
        return $this->authService->getUserByToken($user->getToken());
    }

    public function supportsClass(string $class): bool
    {
        return ExternalAuthUser::class === $class
            || is_subclass_of($class, ExternalAuthUser::class);

    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->authService->getUserByToken($identifier);
    }
}
