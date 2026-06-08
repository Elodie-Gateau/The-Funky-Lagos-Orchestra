<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

readonly class PhotoConversionService
{
    public function __construct(private LoggerInterface $logger) {}
    public function convertToWebp(string $inputPath, string $outputPath, int $maxWidth = 1200, int $maxHeight = 1200, int $quality = 82): string
    {
        $scaleFilter = "scale='min({$maxWidth},iw)':'min({$maxHeight},ih)':force_original_aspect_ratio=decrease";

        $process = new Process([
            'ffmpeg', '-i', $inputPath, '-vf', $scaleFilter, '-quality', (string) $quality, '-y', $outputPath,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('FFmpeg image conversion error', [
                'input'  => $inputPath,
                'output' => $outputPath,
                'error'  => $process->getErrorOutput(),
            ]);
            throw new \RuntimeException('La conversion de l\'image a échoué : ' . $process->getErrorOutput());
        }

        return $outputPath;
    }
}
