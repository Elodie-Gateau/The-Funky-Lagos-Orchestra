<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('name')]
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[Assert\NotBlank(message: "L'année' est obligatoire")]
    #[Assert\GreaterThan(1900)]
    #[Assert\LessThan(2100)]
    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column]
    private ?string $cover = null;

    #[ORM\OneToMany(targetEntity: Track::class, mappedBy: 'album', cascade: ['remove'])]
    private Collection $tracks;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function setTracks(Collection $tracks): void
    {
        $this->tracks = $tracks;
    }



}
