<?php

namespace App\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;

readonly class CheckTrackVisibility
{
    public function __construct(
        private TrackRepository $tracksRepository,
    )
    {
    }
    public function checkVisibility(Track $track): array
    {
        if ($this->tracksRepository->count(['visibility' => true]) >= 6) {
            return ['error' => "Il ne peut pas y avoir plus de 6 morceaux publiés en page d'accueil. Merci d'en désactiver un pour ajouter celui-ci."];
        }

        return [];
    }

}
