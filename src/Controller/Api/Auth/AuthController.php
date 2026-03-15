<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['authenticated' => false], 401);
        }
        return $this->json([
            'authenticated' => true,
            'roles' => $user->getRoles(),
            'email' => $user->getUserIdentifier(),
            'surname' => $user->getSurname(),
        ]);
    }

    #[Route('/logout', methods: ['POST'])]
    public function logout(): void
    {
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Symfony intercepte cette requête avant d'arriver ici
        return $this->json(['error' => 'Non intercepté par le firewall'], 500);
    }
}
