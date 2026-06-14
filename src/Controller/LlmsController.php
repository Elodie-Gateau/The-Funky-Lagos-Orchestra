<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\MusicianRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LlmsController extends AbstractController
{
    #[Route('/llms.txt', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository, MusicianRepository $musicianRepository): Response
    {
        $albums = $albumRepository->findAll();
        $musicians = $musicianRepository->findAll();
        $countMusicians = count($musicians);

        $content = "# The Funky Lagos Orchestra\n\n";
        $content .= "> French instrumental afrobeat and afrofunk band, inspired by Fela Kuti. "
            . "{$countMusicians} musicians performing original compositions blending afrobeat, funk and afrofunk.\n\n";

        $content .= "## Band\n";
        $content .= "- [Homepage](https://thefunkylagosorchestra.com): main website\n";
        $content .= "- [Music](https://thefunkylagosorchestra.com/music): discography and tracks\n";
        $content .= "- [About](https://thefunkylagosorchestra.com#about): band history and members\n";
        $content .= "- [Concerts](https://thefunkylagosorchestra.com#events): upcoming and past shows\n\n";
        $content .= "- [Gallery](https://thefunkylagosorchestra.com#gallery): band photos\n\n";

        $content .= "## Members\n";
        foreach ($musicians as $m) {
            $content .= "- {$m->getFirstname()} {$m->getLastname()}: {$m->getInstrument()}\n";
        }

        $content .= "\n## Discography\n";
        foreach ($albums as $album) {
            $content .= "\n### {$album->getName()}\n";
            foreach ($album->getTracks() as $track) {
                $content .= "- \"{$track->getTitle()}\"";
                if ($track->getDescription()) {
                    $content .= "  {$track->getDescription()}\n";
                }
            }
        }

        $content .= "\n## Genre\n";
        $content .= "Afrobeat, afrofunk, funk, instrumental\n";

        return new Response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
