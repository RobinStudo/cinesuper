<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GiftRepository")
 */
class Gift
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $serial;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card", inversedBy="gifts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $card;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GiftType", inversedBy="gifts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $giftType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getGiftType(): ?GiftType
    {
        return $this->giftType;
    }

    public function setGiftType(?GiftType $giftType): self
    {
        $this->giftType = $giftType;

        $this->setExpiredAt($giftType->getDuration());

        return $this;
    }

    public function __toString()
    {
        return $this->serial;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt($duration): self
    {
        $now = new \DateTime();

        $willExpireIn = new \DateInterval("P0D");
        $willExpireIn->days = $duration;

        $now->add($willExpireIn);

        $this->expiredAt = $now ;

        return $this;
    }
}