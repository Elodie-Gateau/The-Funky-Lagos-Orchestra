<?php

namespace App\Entity;

use App\Repository\MusicianRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MusicianRepository::class)]
#[UniqueEntity(
    fields: ['lastname', 'firstname'],
    message: 'Ce musicien existe déjà',
)]
class Musician
{
    public const INSTRUMENTS = ['Batterie', 'Guitare', 'Basse', 'Clavier', 'Saxophone', 'Trompette', 'Percussion'];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom doit faire au minimum 3 lettres',
        maxMessage: 'Le nom ne peut pas dépasser 255 caractères'
    )]
    private ?string $lastname;

    #[ORM\Column]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le prénom doit faire au minimum 3 lettres',
        maxMessage: 'Le prénom ne peut pas dépasser 255 caractères'
    )]
    private ?string $firstname;

    #[ORM\Column]
    #[Assert\Choice(choices: self::INSTRUMENTS, message: 'Veuillez choisir un instrument valide')]
    private ?string $instrument;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }
    public function getInstrument(): ?string
    {
        return $this->instrument;
    }

    public function setInstrument(?string $instrument): static
    {
        $this->instrument = $instrument;
        return $this;
    }


}
