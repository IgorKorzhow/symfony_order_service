<?php
declare(strict_types=1);

namespace App\Dto\Order;

use App\Dto\AbstractValidationDto;
use App\Dto\Basket\BasketDto;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class OrderDto extends AbstractValidationDto
{
    #[Assert\NotNull]
    #[Assert\Valid]
    private ?BasketDto $basket;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [DeliveryTypeEnum::class, 'values'])]
    private string $deliveryType;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\+375(25|29|33|44)\d{7}$/',
        message: 'Please enter a valid Belarusian phone number, e.g. +375291234567'
    )]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [OrderStatusEnum::class, 'values'])]
    private string $orderStatus;

    #[Assert\NotBlank]
    private string|int $userId;

    public function __construct(string $deliveryType, string $phone)
    {
        $this->deliveryType = $deliveryType;
        $this->phone = $phone;
        $this->orderStatus = OrderStatusEnum::CREATED->value;
    }

    public function getBasket(): ?BasketDto
    {
        return $this->basket;
    }

    public function setBasket(BasketDto $basket): void
    {
        $this->basket = $basket;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(string $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    public function getUserId(): int|string
    {
        return $this->userId;
    }

    public function setUserId(int|string $userId): void
    {
        $this->userId = $userId;
    }

    public function extendValidation(): ConstraintViolationListInterface
    {
        $errors = parent::extendValidation();

        if ($this->getBasket() === null ||  count($this->getBasket()->getProducts()) < 1) {
            $errors->add(
                new ConstraintViolation(
                    message: 'Basket must have at least one product',
                    messageTemplate: 'ProductCountError',
                    parameters: [],
                    root: $this,
                    propertyPath: 'products',
                    invalidValue: $this->getBasket() ? $this->getBasket()->getProducts() : $this->getBasket(),
                    plural: null,
                    code: null,
                    constraint: null
                )
            );
        }

        $totalProductsCount = array_reduce($this->getBasket()->getProducts(), function ($carry, $product) {
            return $carry + $product->getQuantity();
        }, 0);

        if ($totalProductsCount > 20) {
            $errors->add(
                new ConstraintViolation(
                    message: 'Basket must have less than 20 products',
                    messageTemplate: 'ProductCountError',
                    parameters: [],
                    root: $this,
                    propertyPath: 'products',
                    invalidValue: $this->getBasket()->getProducts(),
                    plural: null,
                    code: null,
                    constraint: null
                )
            );
        }

        return $errors;
    }
}
