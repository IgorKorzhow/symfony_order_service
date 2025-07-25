<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $payedAt = null;

    #[ORM\Column]
    private float $totalPrice = 0;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderItems;

    #[ORM\Column(type: Types::STRING, enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $orderStatus;

    #[ORM\Column(type: Types::STRING, enumType: DeliveryTypeEnum::class)]
    private DeliveryTypeEnum $deliveryType;

    #[ORM\Column]
    private int $userId;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->orderStatus = OrderStatusEnum::CREATED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function getDeliveryType(): DeliveryTypeEnum
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(DeliveryTypeEnum $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    public function getOrderStatus(): OrderStatusEnum
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(OrderStatusEnum $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getPayedAt(): ?\DateTimeImmutable
    {
        return $this->payedAt;
    }

    public function setPayedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->payedAt = $updatedAt;

        return $this;
    }

    public function addOrderItem(OrderItem $item): self
    {
        if (!$this->orderItems->contains($item)) {
            $this->orderItems[] = $item;
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $item): self
    {
        if ($this->orderItems->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }
}
