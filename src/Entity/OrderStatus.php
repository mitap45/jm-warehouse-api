<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="order_status")
* @ORM\Entity(repositoryClass="App\Repository\OrderStatusRepository")
*/
class OrderStatus
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $deliveryDate;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $shippingCode;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="status")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $order;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getShippingCode(): ?string
    {
        return $this->shippingCode;
    }

    public function setShippingCode(string $shippingCode): self
    {
        $this->shippingCode = $shippingCode;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }
}
