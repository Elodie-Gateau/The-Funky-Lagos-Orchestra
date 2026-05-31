<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('title')]
#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $Added_at = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le fichier audio est obligatoire')]
    #[Assert\Length(max: 255)]
    private ?string $audioFile = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $duration = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'artiste est obligatoire")]
    #[Assert\Length(max: 255)]
    private ?string $artist = null;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'tracks')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Album $album;

    #[ORM\Column]
    private ?bool $visibility = false;

    #[ORM\Column(nullable: true)]
    private ?int $homePosition = null;

    #[ORM\Column(nullable: true)]
    private ?int $albumPosition = null;

    public function __construct()
    {
        $this->Added_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->Added_at;
    }

    public function setAddedAt(\DateTimeImmutable $Added_at): static
    {
        $this->Added_at = $Added_at;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getAudioFile(): ?string
    {
        return $this->audioFile;
    }

    public function setAudioFile(string $audioFile): static
    {
        $this->audioFile = $audioFile;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(Album $album): static
    {
        $this->album = $album;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getHomePosition(): ?int
    {
        return $this->homePosition;
    }

    public function setHomePosition(?int $homePosition): static
    {
        $this->homePosition = $homePosition;

        return $this;
    }

    public function getAlbumPosition(): ?int
    {
        return $this->albumPosition;
    }

    public function setAlbumPosition(?int $albumPosition): static
    {
        $this->albumPosition = $albumPosition;

        return $this;
    }
}
