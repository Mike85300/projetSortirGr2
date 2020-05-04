<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TripRepository")
 * @ORM\Table(name="trips")
 */
class Trip
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Le nom de la sortie ne peut pas dépasser 50 caractères.",
     *      allowEmptyString = false,
     *      normalizer="trim",
     * )
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Veuillez renseigner un nom.",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(
     *     message="Vous devez renseigner une date et une heure de début."
     * )
     * @Assert\GreaterThan(
     *     value="now",
     *     message="La date de début doit être postérieure à {{ compared_value }}."
     * )
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Veuillez renseigner une date et une heure de début.",
     * )
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="int",
     *     message="Cette valeur doit être un nombre",
     * )
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Veuillez renseigner une durée.",
     * )
     * @Assert\Positive (
     *     message="Ce nombre doit être positif.",
     * )
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(
     *     message="Vous devez renseigner une date et une heure limite d'inscription."
     * )
     * @Assert\LessThan(
     *     propertyPath="startDate",
     *     message="La date de limite d'inscription doit être antérieure à {{ compared_value }}."
     * )
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Veuillez renseigner un date limite et une heure limite d'inscription.",
     * )
     */
    private $registrationDeadline;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="int",
     *     message="Cette valeur doit être un nombre.",
     * )
     * @Assert\Positive(
     *     message="Ce nombre doit être positif.",
     * )
     */
    private $maxRegistrationNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Positive
     */
    private $information;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=true)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\State")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *     normalizer="trim",
     *     message="Veuillez renseigner un lieu.",
     * )
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $organizer;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="registredTrips")
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

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

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): self
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getMaxRegistrationNumber(): ?int
    {
        return $this->maxRegistrationNumber;
    }

    public function setMaxRegistrationNumber(?int $maxRegistrationNumber): self
    {
        $this->maxRegistrationNumber = $maxRegistrationNumber;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }
}
