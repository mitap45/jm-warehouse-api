<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="`order`")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $orderNo;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $orderDate;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $maxShippingDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shippingAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customerName;

    /**
     * @ORM\Column(type="integer")
     */
    private $shippingCity;

    /**
     * @ORM\Column(type="integer")
     */
    private $shippingRegion;

    /**
     * @ORM\Column(type="integer")
     */
    private $postalCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="order")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderStatus", mappedBy="order")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="givenOrders")
     * @ORM\JoinColumn(name="ecommerce_company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $ecommerceCompany;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="takenOrders")
     * @ORM\JoinColumn(name="cargo_company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $cargoCompany;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->status = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNo(): ?string
    {
        return $this->orderNo;
    }

    public function setOrderNo(string $orderNo): self
    {
        $this->orderNo = $orderNo;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(string $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getShippingCity(): ?int
    {
        return $this->shippingCity;
    }

    public function setShippingCity(int $shippingCity): self
    {
        $this->shippingCity = $shippingCity;

        return $this;
    }

    public function getShippingRegion(): ?int
    {
        return $this->shippingRegion;
    }

    public function setShippingRegion(int $shippingRegion): self
    {
        $this->shippingRegion = $shippingRegion;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderProduct $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderProduct $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrderStatus[]
     */
    public function getStatus(): Collection
    {
        return $this->status;
    }

    public function addStatus(OrderStatus $status): self
    {
        if (!$this->status->contains($status)) {
            $this->status[] = $status;
            $status->setOrder($this);
        }

        return $this;
    }

    public function removeStatus(OrderStatus $status): self
    {
        if ($this->status->contains($status)) {
            $this->status->removeElement($status);
            // set the owning side to null (unless already changed)
            if ($status->getOrder() === $this) {
                $status->setOrder(null);
            }
        }

        return $this;
    }

    public function getEcommerceCompany(): ?User
    {
        return $this->ecommerceCompany;
    }

    public function setEcommerceCompany(?User $ecommerceCompany): self
    {
        $this->ecommerceCompany = $ecommerceCompany;

        return $this;
    }

    public function getCargoCompany(): ?User
    {
        return $this->cargoCompany;
    }

    public function setCargoCompany(?User $cargoCompany): self
    {
        $this->cargoCompany = $cargoCompany;

        return $this;
    }

    public function getMaxShippingDate(): ?\DateTimeInterface
    {
        return $this->maxShippingDate;
    }

    public function setMaxShippingDate(\DateTimeInterface $maxShippingDate): self
    {
        $this->maxShippingDate = $maxShippingDate;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }
}
