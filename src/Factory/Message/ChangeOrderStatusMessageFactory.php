<?php

namespace App\Factory\Message;

use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Message\Order\ChangeOrderStatusMessage;
use App\Validator\ExistsEntityByField;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ChangeOrderStatusMessageFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @throws Exception
     */
    public function fromArray(array $data): ChangeOrderStatusMessage
    {
        $this->validateData($data);

        return new ChangeOrderStatusMessage(
            orderId: $data['orderId'],
            status: $data['status'],
            payedAt: $data['payedAt'] ? new DateTimeImmutable($data['payedAt']) : null,
        );
    }

    private function validateData(array $data): void
    {
        $violations = $this->validator->validate($data, $this->validationConstraints());

        if (count($violations) > 0) {
            throw new \InvalidArgumentException((string) $violations);
        }
    }

    private function validationConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'id' => [
                new Assert\NotNull(),
                new Assert\Type(type: 'integer'),
                new ExistsEntityByField(entityClass: Order::class, field: 'id'),
            ],
            'status' => [
                new Assert\NotNull(),
                new Assert\Choice(callback: [OrderStatusEnum::class, 'values'])],
            'payedAt' => [
                new Assert\NotNull(),
                new Assert\DateTime(format: 'Y-m-d')],
        ]);
    }
}
