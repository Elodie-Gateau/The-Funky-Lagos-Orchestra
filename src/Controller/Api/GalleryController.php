<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class GalleryController extends AbstractController
{
    #[Route('/photos', methods: ['GET'])]
    public function getPhotos(PhotoRepository $photoRepository): JsonResponse
    {
        return $this->json([
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/photo/add', methods: ['POST'])]
    public function addPhoto(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $imageFile = $request->files->get('image');

        if (!$imageFile || $imageFile->getError() !== UPLOAD_ERR_OK) {
            return $this->json(['error' => 'Fichier manquant'], 400);
        }
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($imageFile->getMimeType(), $allowedMimes)) {
            return $this->json(['error' => 'Format non autorisé'], 400);
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $imageFile->guessExtension();
        $imageFile->move(
            $this->getParameter('kernel.project_dir') . '/public/images/gallery',
            $filename
        );

        $photo = new Photo();
        $photo->setPath('/images/gallery/' . $filename);

        $em->persist($photo);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/photo/{id}/delete', methods: ['DELETE'])]
    public function deletePhoto(
        Photo $photo,
        EntityManagerInterface $em
    ): JsonResponse {
        $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $photo->getPath();
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }

        $em->remove($photo);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
