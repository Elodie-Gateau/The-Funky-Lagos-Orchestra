<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Tracks;
use App\Repository\TracksRepository;
use App\Service\AudioConversionService;
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
    )
    {}

    #[Route('/music', methods: ['GET'])]
    public function getTracks(TracksRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findAll();
        return $this->json([
            'tracks' => $tracks,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/music/add', methods: ['POST'])]
    public function addTrack(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $title = $request->request->get('title');
        $artist = $request->request->get('artist');
        $audioFile = $request->files->get('audioFile');
        $album = $request->request->get('album');
        $status = $request->request->get('status');
        $duration = $request->request->get('duration');
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
            . '/public/audio/' . uniqid() . '.mp3';
        $finalAudioPath = $this->audioConversion->convertToMp3($tempPath, $outputPath);

        $track = new Tracks();
        $track->setTitle($title);
        $track->setArtist($artist);
        $track->setAlbum($album);
        $track->setDuration($duration);
        $track->setAudioFile('/audio/' . basename($finalAudioPath));
        $track->setStatus($status);

        $em->persist($track);
        $em->flush();

        return $this->json(['success' => true]);
    }

}
