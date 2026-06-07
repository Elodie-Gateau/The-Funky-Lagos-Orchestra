<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

readonly class AudioConversionService
{
    public function __construct(private LoggerInterface $logger) {}
    public function convertToMp3(string $inputPath, string $outputPath): string
    {
        $process = new Process([
            'ffmpeg', '-i', $inputPath, '-t', '30', '-q:a', '0', '-map', 'a', $outputPath
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('FFmpeg error: ' . $process->getErrorOutput());
            throw new \RuntimeException('La conversion audio a échoué.');
        }

        return $outputPath;
    }
}
