<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *     min=3,
     *     max=80,
     *     minMessage="Le nom de l'évènement doit faire au moins {{ limit }} caractères.",
     *     maxMessage="Le nom de l'évènement ne peut dépasser {{ limit }} caractères."
     * )
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endAt;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min="1",
     *     minMessage="La durée de l'évènement doit au moins être égale à {{ limit }} jours."
     * )
     * 
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min="2",
     *     minMessage="Le multiplicateur doit être un chiffre et être au moins égal à {{ limit }}."
     * )
     * 
     */
    private $multiplicateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEndAt(): self
    {
        $dateOfEnd = new \DateTime($this->startAt->format('Y-m-d H:i:s'));

        $interval = "+" . $this->duration . " day";

        $dateOfEnd->modify($interval);

        $this->endAt = $dateOfEnd;

        return $this;
    }

    public function getMultiplicateur(): ?int
    {
        return $this->multiplicateur;
    }

    public function setMultiplicateur(int $multiplicateur): self
    {
        $this->multiplicateur = $multiplicateur;

        return $this;
    }
}
