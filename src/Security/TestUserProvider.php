<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\ExternalAuthUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TestUserProvider implements UserProviderInterface
{
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === ExternalAuthUser::class
            || is_subclass_of($class, ExternalAuthUser::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $roles = explode(',', $identifier);

        return new ExternalAuthUser([
            'id' => 1,
            'email' => 'test@gmail.com',
            'roles' => $roles,
            'phone' => '1234567',
            'name' => 'Test',
            'token' => 'test token',
        ]);
    }
}
