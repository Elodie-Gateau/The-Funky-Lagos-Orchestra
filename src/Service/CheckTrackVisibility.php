<?php

namespace App\Service;

use App\Entity\Tracks;
use App\Repository\TracksRepository;

readonly class CheckTrackVisibility
{
    public function __construct(
        private TracksRepository $tracksRepository,
    )
    {
    }
    public function checkVisibility(Tracks $track): array
    {
        if($track->getStatus() !== "Publié") {
            return ['error' => "Le morceau doit être publié. Merci de changer son statut."];
        }

        if ($this->tracksRepository->count(['isVisible' => true]) >= 6) {
            return ['error' => "Il ne peut pas y avoir plus de 6 morceaux publiés en page d'accueil. Merci d'en désactiver un pour ajouter celui-ci."];
        }

        return [];
    }

}
