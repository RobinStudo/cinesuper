<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $number;

    /**
     * @ORM\Column(type="integer")
     */
    private $fidelity = 0;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="card", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift", mappedBy="card", orphanRemoval=true)
     */
    private $gifts;

    public function __construct()
    {
        $this->gifts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getFidelity(): ?int
    {
        return $this->fidelity;
    }

    public function setFidelity(int $fidelity): self
    {
        $this->fidelity = $fidelity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // set the owning side of the relation if necessary
        if ($user->getCard() !== $this) {
            $user->setCard($this);
        }

        return $this;
    }

    /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $voucher): self
    {
        if (!$this->gifts->contains($voucher)) {
            $this->gifts[] = $voucher;
            $voucher->setCard($this);
        }

        return $this;
    }

    public function removeGift(Gift $voucher): self
    {
        if ($this->gifts->contains($voucher)) {
            $this->gifts->removeElement($voucher);
            // set the owning side to null (unless already changed)
            if ($voucher->getCard() === $this) {
                $voucher->setCard(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->number;
    }
}
