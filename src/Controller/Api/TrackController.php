<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Album;
use App\Entity\Track;
use App\Repository\AlbumRepository;
use App\Repository\TrackRepository;
use App\Service\AudioConversionService;
use App\Service\CheckTrackVisibility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api')]
class TrackController extends AbstractController
{


    public function __construct(
        private readonly AudioConversionService $audioConversion,
        private readonly CheckTrackVisibility $checkTrackVisibility,
        private readonly AlbumRepository $albumRepository,
    )
    {}

    #[Route('/tracks', methods: ['GET'])]
    public function getTracks(TrackRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findAll();
        return $this->json([
            'tracks' => array_map(fn(Track $track) => [
                'id'        => $track->getId(),
                'title'     => $track->getTitle(),
                'artist'    => $track->getArtist(),
                'duration'  => $track->getDuration(),
                'audioFile' => $track->getAudioFile(),
                'visibility' => $track->isVisible(),
                'album'     => [
                    'id'    => $track->getAlbum()?->getId(),
                    'name' => $track->getAlbum()?->getName(),
                    'year' => $track->getAlbum()?->getYear(),
                    'cover' => $track->getAlbum()?->getCover(),
                ],
            ], $tracks),
        ]);
    }

    #[Route('/tracks/home', methods: ['GET'])]
    public function getVisibleTracks(TrackRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findby(['visibility' => true]);
        return $this->json([
            'tracks' => array_map(fn(Track $track) => [
                'id'        => $track->getId(),
                'title'     => $track->getTitle(),
                'artist'    => $track->getArtist(),
                'duration'  => $track->getDuration(),
                'audioFile' => $track->getAudioFile(),
                'visibility' => $track->isVisible(),
                'album'     => [
                    'id'    => $track->getAlbum()?->getId(),
                    'name' => $track->getAlbum()?->getName(),
                    'year' => $track->getAlbum()?->getYear(),
                    'cover' => $track->getAlbum()?->getCover(),
                ],
            ], $tracks),
        ]);
    }

    #[Route('/tracks/others', methods: ['GET'])]
    public function getOthersTracks(TrackRepository $tracksRepository): JsonResponse
    {
        $tracks = $tracksRepository->findby(['visibility' => false]);
        return $this->json([
            'tracks' => array_map(fn(Track $track) => [
                'id'        => $track->getId(),
                'title'     => $track->getTitle(),
                'artist'    => $track->getArtist(),
                'duration'  => $track->getDuration(),
                'audioFile' => $track->getAudioFile(),
                'visibility' => $track->isVisible(),
                'album'     => [
                    'id'    => $track->getAlbum()?->getId(),
                    'name' => $track->getAlbum()?->getName(),
                    'year' => $track->getAlbum()?->getYear(),
                    'cover' => $track->getAlbum()?->getCover(),
                ],
            ], $tracks),
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/track/add', methods: ['POST'])]
    public function addTrack(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $track = new Track();
        $track->setTitle($request->request->get('title'));
        $track->setArtist('The Funky Lagos Orchestra');
        $track->setDuration($request->request->get('duration'));
        $track->setVisibility($request->request->get('visibility') === 'true');

        $albumId = (int) $request->request->get('album');
        if (!$albumId) {
            return $this->json(['error' => 'Album manquant'], 400);
        }
        $album = $this->albumRepository->find($albumId);

        if (!$album) {
            return $this->json(['error' => 'Album inexistant'], 400);
        }

        assert($album instanceof Album);

        $track->setAlbum($album);

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
        if($track->isVisible()) {
            $checkTrack = $this->checkTrackVisibility->checkVisibility($track);
            if (isset($checkTrack['error'])) {
                return $this->json($checkTrack, 400);
            }
        }
        $em->persist($track);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/track/{id}/delete', methods: ['DELETE'])]
    public function deleteTrack(Track $track, EntityManagerInterface $em): JsonResponse
    {
        if ($track->isVisible()) {
            return $this->json(['error' => 'Impossible de supprimer un track visible sur la home page'], 400);
        }
        $audioPath = $this->getParameter('kernel.project_dir') . '/public' . $track->getAudioFile();
        if (file_exists($audioPath)) {
            unlink($audioPath);
        }

        $em->remove($track);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/admin/track/{id}', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateTrack(Track $track, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $track->setTitle($request->request->get('title'));
        $track->setArtist('The Funky Lagos Orchestra');
        $track->setDuration($request->request->get('duration'));
        $track->setVisibility($request->request->get('visibility') === 'true');
        $audioFile = $request->files->get('audioFile');

        $albumId = (int) $request->request->get('album');
        if (!$albumId) {
            return $this->json(['error' => 'Album manquant'], 400);
        }
        $album = $this->albumRepository->find($albumId);

        if (!$album) {
            return $this->json(['error' => 'Album inexistant'], 400);
        }

        assert($album instanceof Album);

        $track->setAlbum($album);

        if ($audioFile && $audioFile->getError() === UPLOAD_ERR_OK) {
            $tempPath = $audioFile->getRealPath();
            $outputPath = $this->getParameter('kernel.project_dir')
                . '/public/audio/' . bin2hex(random_bytes(16)) . '.mp3';
            $finalAudioPath = $this->audioConversion->convertToMp3($tempPath, $outputPath);

            $track->setAudioFile('/audio/' . basename($finalAudioPath));
        }
        if($track->isVisible()){
            $checkTrack = $this->checkTrackVisibility->checkVisibility($track);
            if (isset($checkTrack['error'])) {
                return $this->json($checkTrack, 400);
            }
        }
        $em->persist($track);
        $em->flush();
        return $this->json(['success' => true]);
    }

}
