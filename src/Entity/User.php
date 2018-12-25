<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user")
     */
    private $tokens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="ecommerceCompany")
     */
    private $givenOrders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="cargoCompany")
     */
    private $takenOrders;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->givenOrders = new ArrayCollection();
        $this->takenOrders = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getGivenOrders(): Collection
    {
        return $this->givenOrders;
    }

    public function addGivenOrder(Order $givenOrder): self
    {
        if (!$this->givenOrders->contains($givenOrder)) {
            $this->givenOrders[] = $givenOrder;
            $givenOrder->setEcommerceCompany($this);
        }

        return $this;
    }

    public function removeGivenOrder(Order $givenOrder): self
    {
        if ($this->givenOrders->contains($givenOrder)) {
            $this->givenOrders->removeElement($givenOrder);
            // set the owning side to null (unless already changed)
            if ($givenOrder->getEcommerceCompany() === $this) {
                $givenOrder->setEcommerceCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getTakenOrders(): Collection
    {
        return $this->takenOrders;
    }

    public function addTakenOrder(Order $takenOrder): self
    {
        if (!$this->takenOrders->contains($takenOrder)) {
            $this->takenOrders[] = $takenOrder;
            $takenOrder->setCargoCompany($this);
        }

        return $this;
    }

    public function removeTakenOrder(Order $takenOrder): self
    {
        if ($this->takenOrders->contains($takenOrder)) {
            $this->takenOrders->removeElement($takenOrder);
            // set the owning side to null (unless already changed)
            if ($takenOrder->getCargoCompany() === $this) {
                $takenOrder->setCargoCompany(null);
            }
        }

        return $this;
    }

}
