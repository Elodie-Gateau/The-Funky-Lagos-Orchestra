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
        $photo = new Photo();
        $imageFile = $request->files->get('image');

        $photo->setPath($imageFile);

        $em->persist($photo);
        $em->flush();
        return $this->json(['success' => true]);
    }

}
