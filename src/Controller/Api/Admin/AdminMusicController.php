<?php

declare(strict_types=1);

namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminMusicController extends AbstractController
{
    #[Route('/manage-music')]
    public function index(): Response
    {
        return $this->render('manage_music/index.html.twig');
    }
}
