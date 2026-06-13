<?php

namespace App\Controller\Api;

use App\Entity\Musician;
use App\Repository\MusicianRepository;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class MusicianController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EntityValidator        $validator,
    )
    {}

    #[Route('/musicians', methods: ['GET'])]
    public function getMusicians(MusicianRepository $musicianRepository): JsonResponse
    {
        $musicians = array_map(fn(Musician $musician) => [
            'id' => $musician->getId(),
            'firstname' => $musician->getFirstname(),
            'lastname' => $musician->getLastname(),
            'instrument' => $musician->getInstrument(),
            'fullName' => $musician->getFirstname() . ' ' . $musician->getLastname(),
            'fullNameWithInstrument' => $musician->getFirstname() . ' ' . $musician->getLastname() . ' - ' . $musician->getInstrument()
        ], $musicianRepository->findAll());

        return $this->json([
            'musicians' => $musicians,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/musician/add', methods: ['POST'])]
    public function addMusician(Request $request): JsonResponse
    {
        $musician = new Musician();
        $musician->setFirstname($request->request->get('firstname'));
        $musician->setLastname($request->request->get('lastname'));
        $musician->setInstrument($request->request->get('instrument'));

        $errors = $this->validator->validate($musician);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }
        $this->em->persist($musician);
        $this->em->flush();
        return $this->json(['success' => true], 201);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/musician/update/{id}', methods: ['POST'])]
    public function updateMusician(Musician $musician, Request $request): JsonResponse
    {
        $musician->setFirstname($request->request->get('firstname'));
        $musician->setLastname($request->request->get('lastname'));
        $musician->setInstrument($request->request->get('instrument'));

        $errors = $this->validator->validate($musician);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 422);
        }
        $this->em->flush();
        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/musician/delete/{id}', methods: ['POST'])]
    public function deleteMusician(Musician $musician): JsonResponse
    {
        $this->em->remove($musician);
        $this->em->flush();
        return $this->json(['success' => true]);
    }

}
