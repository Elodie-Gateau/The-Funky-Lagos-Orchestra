<?php

namespace App\Tests\Unit\Service;

use App\Service\AudioConversionService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AudioConversionServiceTest extends TestCase
{
    private LoggerInterface $logger;
    private AudioConversionService $service;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->service = new AudioConversionService($this->logger);
    }

    public function testConversionEchoueEtLanceException(): void
    {
        $inputFile = tempnam(sys_get_temp_dir(), 'audio_test_');
        file_put_contents($inputFile, 'contenu invalide non audio');
        $outputPath = sys_get_temp_dir() . '/output_test_' . uniqid() . '.mp3';

        $this->logger->expects($this->once())->method('error');

        try {
            $this->service->convertToMp3($inputFile, $outputPath);
            $this->fail('RuntimeException attendue');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('conversion', strtolower($e->getMessage()));
        } finally {
            @unlink($inputFile);
            @unlink($outputPath);
        }
    }

    public function testCopieFichierDejaEnMp3(): void
    {
        $inputFile = tempnam(sys_get_temp_dir(), 'mp3_test_');
        $outputFile = sys_get_temp_dir() . '/output_mp3_' . uniqid() . '.mp3';

        // Entête ID3v2 minimale pour que mime_content_type détecte audio/mpeg
        $id3Header = "ID3\x03\x00\x00\x00\x00\x00\x00";
        $mp3FrameHeader = "\xff\xfb\x90\x00";
        file_put_contents($inputFile, $id3Header . $mp3FrameHeader . str_repeat("\x00", 400));

        $mimeType = mime_content_type($inputFile);

        if (!in_array($mimeType, ['audio/mpeg', 'audio/mp3'])) {
            $this->markTestSkipped('Le fichier de test n\'est pas détecté comme audio/mpeg sur cet environnement (détecté: ' . $mimeType . ')');
        }

        try {
            $result = $this->service->convertToMp3($inputFile, $outputFile);

            $this->assertSame($outputFile, $result);
            $this->assertFileExists($outputFile);
        } finally {
            @unlink($inputFile);
            @unlink($outputFile);
        }
    }

    public function testLancerExceptionSiFichierNonAudio(): void
    {
        $inputFile = tempnam(sys_get_temp_dir(), 'audio_invalid_');
        file_put_contents($inputFile, 'ceci est du texte, pas de laudio');
        $outputPath = sys_get_temp_dir() . '/output_invalid_' . uniqid() . '.mp3';

        $this->logger->expects($this->once())->method('error');

        try {
            $this->service->convertToMp3($inputFile, $outputPath);
            $this->fail('RuntimeException attendue');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('conversion', strtolower($e->getMessage()));
        } finally {
            @unlink($inputFile);
            @unlink($outputPath);
        }
    }
}
