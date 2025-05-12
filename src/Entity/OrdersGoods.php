<?php

namespace App\Entity;

use App\Repository\OrdersGoodsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersGoodsRepository::class)]
class OrdersGoods
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orderId;

    #[ORM\Column]
    private ?int $productId;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $count;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $lockedPrice;

    #[ORM\ManyToOne(inversedBy: 'ordersGoods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $orders;

    #[ORM\ManyToOne(inversedBy: 'ordersGoods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Goods $goods;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getLockedPrice(): ?string
    {
        return $this->lockedPrice;
    }

    public function setLockedPrice(string $lockedPrice): static
    {
        $this->lockedPrice = $lockedPrice;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    public function getGoods(): ?Goods
    {
        return $this->goods;
    }

    public function setGoods(?Goods $goods): static
    {
        $this->goods = $goods;

        return $this;
    }
}
