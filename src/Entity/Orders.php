<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $customer;

    /**
     * @var Collection<int, OrdersGoods>
     */
    #[ORM\OneToMany(targetEntity: OrdersGoods::class, mappedBy: 'orders', orphanRemoval: true)]
    private Collection $ordersGoods;

    public function __construct()
    {
        $this->ordersGoods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Users
    {
        return $this->customer;
    }

    public function setCustomer(?Users $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, OrdersGoods>
     */
    public function getOrdersGoods(): Collection
    {
        return $this->ordersGoods;
    }

    public function addOrdersGood(OrdersGoods $ordersGood): static
    {
        if (!$this->ordersGoods->contains($ordersGood)) {
            $this->ordersGoods->add($ordersGood);
            $ordersGood->setOrders($this);
        }

        return $this;
    }

    public function removeOrdersGood(OrdersGoods $ordersGood): static
    {
        if ($this->ordersGoods->removeElement($ordersGood)) {
            // set the owning side to null (unless already changed)
            if ($ordersGood->getOrders() === $this) {
                $ordersGood->setOrders(null);
            }
        }

        return $this;
    }
}
