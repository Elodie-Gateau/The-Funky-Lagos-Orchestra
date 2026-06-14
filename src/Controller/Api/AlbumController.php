<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Album;
use App\Entity\Track;
use App\Repository\AlbumRepository;
use App\Service\PhotoConversionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class AlbumController extends AbstractController
{
    public function __construct(private readonly PhotoConversionService $photoConversionService) {}
    #[Route('/albums', methods: ['GET'])]
    public function getAlbums(AlbumRepository $albumRepository): JsonResponse
    {
        return $this->json([
            'albums' => array_map(function(Album $album) {
                $tracks = $album->getTracks()->toArray();
                usort($tracks, fn($a, $b) =>
                    ($a->getAlbumPosition() ?? PHP_INT_MAX) <=> ($b->getAlbumPosition() ?? PHP_INT_MAX)
                );
                return [
                    'id'     => $album->getId(),
                    'name'   => $album->getName(),
                    'year'   => $album->getYear(),
                    'cover'  => $album->getCover(),
                    'tracks' => array_map(fn(Track $track) => [
                        'id'            => $track->getId(),
                        'title'         => $track->getTitle(),
                        'artist'        => $track->getArtist(),
                        'duration'      => $track->getDuration(),
                        'audioFile'     => $track->getAudioFile(),
                        'isVisible'     => $track->isVisible(),
                        'homePosition'  => $track->getHomePosition(),
                        'albumPosition' => $track->getAlbumPosition(),
                        'album'         => [
                            'id'    => $album->getId(),
                            'name'  => $album->getName(),
                            'year'  => $album->getYear(),
                            'cover' => $album->getCover(),
                        ],
                    ], $tracks),
                ];
            }, $albumRepository->findAll()),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/album/add', methods: ['POST'])]
    public function addAlbum(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $album = new album();
        $album->setName($request->request->get('name'));
        $yearRaw = $request->request->get('year');
        $album->setYear($yearRaw !== null && $yearRaw !== '' ? (int) $yearRaw : null);
        $coverFile = $request->files->get('cover');

        if (!$coverFile || $coverFile->getError() !== UPLOAD_ERR_OK) {
            return $this->json(['error' => 'Fichier manquant'], 400);
        }
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($coverFile->getMimeType(), $allowedMimes)) {
            return $this->json(['error' => 'Format non autorisé'], 400);
        }
        $filename = bin2hex(random_bytes(16)) . '.webp';
        $destPath = $this->getParameter('kernel.project_dir') . '/public/images/albums/' . $filename;
        $this->photoConversionService->convertToWebp($coverFile->getPathname(), $destPath, 150, 150);

        $album->setCover('/images/albums/' . $filename);

        $em->persist($album);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/admin/album/{id}', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateAlbum(Album $album, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $album->setName($request->request->get('name'));
        $yearRaw = $request->request->get('year');
        $album->setYear($yearRaw !== null && $yearRaw !== '' ? (int) $yearRaw : null);

        $coverFile = $request->files->get('cover');

        if ($coverFile && $coverFile->getError() === UPLOAD_ERR_OK) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($coverFile->getMimeType(), $allowedMimes)) {
                return $this->json(['error' => 'Format non autorisé'], 400);
            }
            $oldCover = $album->getCover();
            if ($oldCover) {
                $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $oldCover;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $albumsDir = $this->getParameter('kernel.project_dir') . '/public/images/albums';
            $filename = bin2hex(random_bytes(16)) . '.webp';
            $destPath = $albumsDir . '/' . $filename;

            $this->photoConversionService->convertToWebp(
                $coverFile->getPathname(),
                $destPath,
                150, 150
            );

            $album->setCover('/images/albums/' . $filename);
        }

        $em->persist($album);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/album/{id}/delete', methods: ['DELETE'])]
    public function deleteAlbum(Album $album, EntityManagerInterface $em): JsonResponse
    {
        foreach ($album->getTracks() as $track) {
            if ($track->isVisible()) {
                return $this->json(['error' => 'Impossible de supprimer un album avec des tracks visibles sur la home page'], 400);
            }
        }

        foreach ($album->getTracks() as $track) {
            $audioPath = $this->getParameter('kernel.project_dir') . '/public' . $track->getAudioFile();
            if (file_exists($audioPath)) {
                unlink($audioPath);
            }
        }


        $cover = $album->getCover();
        if ($cover) {
            $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $cover;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }


        $em->remove($album);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
