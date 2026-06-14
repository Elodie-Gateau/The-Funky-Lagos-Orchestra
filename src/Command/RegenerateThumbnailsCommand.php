<?php

namespace App\Command;

use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use App\Repository\SettingRepository;
use App\Service\PhotoConversionService;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly SettingRepository $settingRepository,
        private readonly PhotoConversionService $photoConversionService,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Albums covers : 150x150
        $albums = $this->albumRepository->findAll();
        $io->section(sprintf('Albums (%d)', count($albums)));
        foreach ($albums as $album) {
            $coverPath = ltrim($album->getCover(), '/');
            $sourcePath = $this->projectDir . '/public/' . $coverPath;

            if (!file_exists($sourcePath)) {
                $io->warning("Fichier introuvable, ignoré : $sourcePath");
                continue;
            }

            $pathInfo = pathinfo($coverPath);
            $isAlreadyWebp = strtolower($pathInfo['extension']) === 'webp';

            if ($isAlreadyWebp) {
                // Régénération en place (cas Nexuma / Baba N'Goma)
                $this->regenerate($sourcePath, 150, 150, $io);
            } else {
                // Conversion initiale PNG -> WebP avec les bonnes dimensions
                $newCoverPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
                $newFullPath = $this->projectDir . '/public/' . $newCoverPath;

                try {
                    $this->photoConversionService->convertToWebp($sourcePath, $newFullPath, 150, 150);
                    $album->setCover('/' . $newCoverPath);
                    $this->entityManager->flush();
                    unlink($sourcePath);
                    $io->writeln("OK (conversion initiale) : $newFullPath");
                } catch (\RuntimeException $e) {
                    $io->error("Échec sur $sourcePath : " . $e->getMessage());
                }
            }
        }

        // Photos galerie : 250x250
        $photos = $this->photoRepository->findAll();
        $io->section(sprintf('Photos galerie (%d)', count($photos)));
        foreach ($photos as $photo) {
            $this->regenerate(
                $this->projectDir . '/public/' . ltrim($photo->getPath(), '/'),
                600,
                600,
                $io
            );
        }

        // Settings (images "about" etc.) : 400x400
        $settings = $this->settingRepository->findAll();
        $io->section('Settings avec image');
        foreach ($settings as $setting) {
            if ($setting->getImage() === null) {
                continue;
            }
            $this->regenerate(
                $this->projectDir . '/public/uploads/' . ltrim($setting->getImage(), '/'),
                400,
                400,
                $io
            );
        }

        $io->success('Régénération terminée.');
        return Command::SUCCESS;
    }

    private function regenerate(string $fullPath, int $width, int $height, SymfonyStyle $io): void
    {
        if (!file_exists($fullPath)) {
            $io->warning("Fichier introuvable, ignoré : $fullPath");
            return;
        }

        $tmpPath = dirname($fullPath) . '/' . uniqid('tmp_') . '.webp';

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
