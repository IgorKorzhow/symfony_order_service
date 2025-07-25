<?php

declare(strict_types=1);

namespace App\Inspectors;

use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class OrderInspector
{
    public function __construct(
        private AuthorizationCheckerInterface $authChecker,
    ) {
    }

    public function canChangeAdminOrderStatus(): bool
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return false;
        }

        return true;
    }

    public function canPay(UserInterface $user, Order $order): bool
    {
        if ($order->getUserId() !== $user->getId()) {
            return false;
        }

        if ($order->getOrderStatus() !== OrderStatusEnum::CREATED) {
            return false;
        }

        return true;
    }
}
