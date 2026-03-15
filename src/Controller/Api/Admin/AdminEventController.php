<?php

declare(strict_types=1);

namespace App\Controller\Api\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminEventController extends AbstractController
{
    #[Route('/manage-events')]
    public function index(): Response
    {
        return $this->render('manage_events/index.html.twig');
    }
}
