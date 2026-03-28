<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class AudioConversionService
{
    public function convertToMp3(string $inputPath, string $outputPath): string
    {
        if ($this->isMp3($inputPath)) {
            return $inputPath; // pas de conversion nécessaire
        }

        $process = new Process([
            'ffmpeg', '-i', $inputPath, '-q:a', '0', '-map', 'a', $outputPath
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $outputPath;
    }

    private function isMp3(string $filePath): bool {
        $mimeType = mime_content_type($filePath);
        return 'audio/mpeg' === $mimeType;
    }

}
