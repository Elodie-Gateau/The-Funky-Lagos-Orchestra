<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class AudioConversionService
{
    public function convertToMp3(string $inputPath, string $outputPath): string
    {
        if ($this->isMp3($inputPath)) {
            copy($inputPath, $outputPath);
            return $outputPath;
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

    private function isMp3(string $filePath): bool
    {
        $mimeType = mime_content_type($filePath);
        return in_array($mimeType, ['audio/mpeg', 'audio/mp3']);
    }

}
