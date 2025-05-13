<?php

namespace App\Entity;

use App\Repository\GoodsRepository;
use App\Trait\UpdateTimestampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GoodsRepository::class)]
#[ORM\Index(name: 'idx_id', columns: ['id'])]
#[ORM\HasLifecycleCallbacks]
class Goods
{
    use UpdateTimestampsTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price;

    #[ORM\Column]
    private int $count;

    /**
     * @var Collection<int, OrdersGoods>
     */
    #[ORM\OneToMany(targetEntity: OrdersGoods::class, mappedBy: 'goods', orphanRemoval: true)]
    private Collection $ordersGoods;


    /**
     * @var Collection<int, Orders>
     */
    #[ORM\ManyToMany(targetEntity: Orders::class, mappedBy: 'goods')]
    private Collection $orders;

    public function __construct()
    {
        $this->ordersGoods = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
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
            $ordersGood->setGoods($this);
        }

        return $this;
    }

    public function removeOrdersGood(OrdersGoods $ordersGood): static
    {
        if ($this->ordersGoods->removeElement($ordersGood)) {
            // set the owning side to null (unless already changed)
            if ($ordersGood->getGoods() === $this) {
                $ordersGood->setGoods(null);
            }
        }

        return $this;
    }

    public function decreaseCount(int $cnt): void
    {
        if ($cnt <= 0) {
            throw new \InvalidArgumentException('Количество товара должно быть > 0');
        }

        if ($this->count < $cnt) {
            throw new \RuntimeException('Недостаточно товаров на складе');
        }

        $this->count -= $cnt;
    }

}
