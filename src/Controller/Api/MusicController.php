<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Tracks;
use App\Repository\TracksRepository;
use App\Service\AudioConversionService;
use App\Service\CheckTrackVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api')]
class MusicController extends AbstractController
{


    public function __construct(
        private readonly AudioConversionService $audioConversion,
        private readonly CheckTrackVisibility $checkTrackVisibility,
    )
    {}

    #[Route('/tracks', methods: ['GET'])]
    public function getTracks(TracksRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findAll();
        return $this->json([
            'tracks' => $tracks,
        ]);
    }

    #[Route('/tracks/home', methods: ['GET'])]
    public function getVisibleTracks(TracksRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findby(['isVisible' => true]);
        return $this->json([
            'tracks' => $tracks,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/tracks/add', methods: ['POST'])]
    public function addTrack(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $track = new Tracks();
        $track->setTitle($request->request->get('title'));
        $track->setArtist($request->request->get('artist'));
        $track->setAlbum($request->request->get('album'));
        $track->setDuration($request->request->get('duration'));
        $track->setStatus($request->request->get('status'));
        $track->setIsVisible($request->request->get('isVisible'));

        $audioFile = $request->files->get('audioFile');
        if (!$audioFile) {
            return $this->json(['error' => 'Fichier audio manquant'], 400);
        }

        if (!$audioFile || $audioFile->getError() !== UPLOAD_ERR_OK) {
            return $this->json([
                'error' => 'Erreur upload fichier: code ' . ($audioFile?->getError() ?? 'null')
            ], 400);
        }
        $tempPath = $audioFile->getRealPath();
        $outputPath = $this->getParameter('kernel.project_dir')
            . '/public/audio/' . bin2hex(random_bytes(16)) . '.mp3';

        $finalAudioPath = $this->audioConversion->convertToMp3($tempPath, $outputPath);

        $track->setAudioFile('/audio/' . basename($finalAudioPath));

        $checkTrack = $this->checkTrackVisibility->checkVisibility($track);
        if (isset($checkTrack['error'])) {
            return $this->json($checkTrack, 400);
        }
        $em->persist($track);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/admin/tracks/{id}', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateTrack(Tracks $track, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $track->setTitle($request->request->get('title'));
        $track->setArtist($request->request->get('artist'));
        $track->setAlbum($request->request->get('album'));
        $track->setDuration($request->request->get('duration'));
        $track->setStatus($request->request->get('status'));
        $track->setIsVisible($request->request->get('isVisible') === 'true');
        $audioFile = $request->files->get('audioFile');

        if ($audioFile && $audioFile->getError() === UPLOAD_ERR_OK) {
            $tempPath = $audioFile->getRealPath();
            $outputPath = $this->getParameter('kernel.project_dir')
                . '/public/audio/' . uniqid() . '.mp3';
            $finalAudioPath = $this->audioConversion->convertToMp3($tempPath, $outputPath);

            $track->setAudioFile('/audio/' . basename($finalAudioPath));
        }

        $checkTrack = $this->checkTrackVisibility->checkVisibility($track);
        if (isset($checkTrack['error'])) {
            return $this->json($checkTrack, 400);
        }
        $em->persist($track);
        $em->flush();
        return $this->json(['success' => true]);
    }
}
