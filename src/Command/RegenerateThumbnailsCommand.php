<?php

namespace App\Command;

use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Service\PhotoConversionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:regenerate-thumbnails', description: 'Régénère les WebP existants avec les nouvelles dimensions')]
class RegenerateThumbnailsCommand extends Command
{
    public function __construct(
        private readonly AlbumRepository $albumRepository,
        private readonly PhotoRepository $photoRepository,
        private readonly PhotoConversionService $photoConversionService,
        private readonly string $imagesDirectory, // param injecté, ex: %kernel.project_dir%/public/images
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Albums : 150x150
        $albums = $this->albumRepository->findAll();
        $io->section(sprintf('Albums (%d)', count($albums)));
        foreach ($albums as $album) {
            $this->regenerate($album->getCoverPath(), $this->imagesDirectory . '/albums', 150, 150, $io);
        }

        // Photos galerie : 250x250
        $photos = $this->photoRepository->findAll();
        $io->section(sprintf('Photos galerie (%d)', count($photos)));
        foreach ($photos as $photo) {
            $this->regenerate($photo->getPath(), $this->imagesDirectory . '/gallery', 250, 250, $io);
        }

        $io->success('Régénération terminée.');
        return Command::SUCCESS;
    }

    private function regenerate(string $relativePath, string $baseDir, int $width, int $height, SymfonyStyle $io): void
    {
        $fullPath = $baseDir . '/' . basename($relativePath);

        if (!file_exists($fullPath)) {
            $io->warning("Fichier introuvable, ignoré : $fullPath");
            return;
        }

        $tmpPath = $fullPath . '.tmp';

        try {
            $this->photoConversionService->convertToWebp($fullPath, $tmpPath, $width, $height);
            rename($tmpPath, $fullPath);
            $io->writeln("OK : $fullPath");
        } catch (\RuntimeException $e) {
            $io->error("Échec sur $fullPath : " . $e->getMessage());
            @unlink($tmpPath);
        }
    }
}
