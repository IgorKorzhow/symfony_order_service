<?php

namespace App\Factory\Message;

use App\Message\Product\Measurement;
use App\Message\Product\ProductMessage;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ProductMessageFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function fromArray(array $data): ProductMessage
    {
        $this->validateData($data);

        return new ProductMessage(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            cost: $data['cost'],
            tax: $data['tax'],
            version: $data['version'],
            measurements: new Measurement(
                weight: $data['measurements']['weight'],
                height: $data['measurements']['height'],
                width: $data['measurements']['width'],
                length: $data['measurements']['length'],
            ),
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
                new Assert\Type('integer'),
            ],
            'name' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
            'description' => new Assert\Optional([
                new Assert\Type('string'),
            ]),
            'cost' => [
                new Assert\NotNull(),
                new Assert\Type('integer'),
                new Assert\PositiveOrZero(),
            ],
            'tax' => [
                new Assert\NotNull(),
                new Assert\Type('integer'),
                new Assert\PositiveOrZero(),
            ],
            'version' => [
                new Assert\NotNull(),
                new Assert\Type('integer'),
                new Assert\GreaterThanOrEqual(1),
            ],
            'measurements' => new Assert\Collection([
                'weight' => [
                    new Assert\NotNull(),
                    new Assert\Type('numeric'),
                    new Assert\PositiveOrZero(),
                ],
                'height' => [
                    new Assert\NotNull(),
                    new Assert\Type('numeric'),
                    new Assert\PositiveOrZero(),
                ],
                'width' => [
                    new Assert\NotNull(),
                    new Assert\Type('numeric'),
                    new Assert\PositiveOrZero(),
                ],
                'length' => [
                    new Assert\NotNull(),
                    new Assert\Type('numeric'),
                    new Assert\PositiveOrZero(),
                ],
            ]),
        ]);
    }
}
