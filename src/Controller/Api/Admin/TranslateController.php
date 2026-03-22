<?php

declare(strict_types=1);

namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslateController extends AbstractController
{
    #[Route('/api/admin/translate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function translate(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        $response = $httpClient->request('POST','https://api-free.deepl.com/v2/translate', [
            'headers' => [
                'Authorization' => 'DeepL-Auth-Key ' . $this->getParameter('deepl_api_key'),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'text' => [$text],
                'source_lang' => 'FR',
                'target_lang' => 'EN'
            ]
        ]);

        $result = $response->toArray();
        $translation = $result['translations'][0]['text'];
        return $this->json(['translation' => $translation]);
    }
}
