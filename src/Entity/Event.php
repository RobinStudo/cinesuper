<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
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
     */
    private $startAt;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *     min="1",
     *     minMessage="La durée de l'évènement doit au moins être égale à un jour."
     * )
     */
    private $duration;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @Assert\Range(
     *     min="1,1",
     *     minMessage="Le multiplicateur doit être un chiffre et être au moins égal à 1,1."
     * )
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

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMultiplicateur(): ?string
    {
        return $this->multiplicateur;
    }

    public function setMultiplicateur(string $multiplicateur): self
    {
        $this->multiplicateur = $multiplicateur;

        return $this;
    }
}
