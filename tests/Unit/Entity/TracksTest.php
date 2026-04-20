<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Tracks;
use PHPUnit\Framework\TestCase;

class TracksTest extends TestCase
{
    public function testConstructeurInitialiseAddedAt(): void
    {
        $before = new \DateTimeImmutable();
        $track = new Tracks();
        $after = new \DateTimeImmutable();

        $this->assertNotNull($track->getAddedAt());
        $this->assertGreaterThanOrEqual($before, $track->getAddedAt());
        $this->assertLessThanOrEqual($after, $track->getAddedAt());
    }

    public function testGetterSetterTitle(): void
    {
        $track = new Tracks();
        $track->setTitle('Funky Groove');

        $this->assertSame('Funky Groove', $track->getTitle());
    }

    public function testGetterSetterArtist(): void
    {
        $track = new Tracks();
        $track->setArtist('The Funky Lagos Orchestra');

        $this->assertSame('The Funky Lagos Orchestra', $track->getArtist());
    }

    public function testGetterSetterAlbum(): void
    {
        $track = new Tracks();
        $track->setAlbum('Lagos Sessions');

        $this->assertSame('Lagos Sessions', $track->getAlbum());
    }

    public function testGetterSetterDuration(): void
    {
        $track = new Tracks();
        $track->setDuration('3:45');

        $this->assertSame('3:45', $track->getDuration());
    }

    public function testGetterSetterAudioFile(): void
    {
        $track = new Tracks();
        $track->setAudioFile('/audio/track.mp3');

        $this->assertSame('/audio/track.mp3', $track->getAudioFile());
    }

    public function testGetterSetterStatusPublie(): void
    {
        $track = new Tracks();
        $track->setStatus('Publié');

        $this->assertSame('Publié', $track->getStatus());
    }

    public function testGetterSetterStatusBrouillon(): void
    {
        $track = new Tracks();
        $track->setStatus('Brouillon');

        $this->assertSame('Brouillon', $track->getStatus());
    }

    public function testGetterSetterIsVisible(): void
    {
        $track = new Tracks();
        $track->setIsVisible(true);

        $this->assertTrue($track->isVisible());

        $track->setIsVisible(false);
        $this->assertFalse($track->isVisible());
    }

    public function testGetterSetterDescription(): void
    {
        $track = new Tracks();
        $track->setDescription('Une description');

        $this->assertSame('Une description', $track->getDescription());
    }

    public function testDescriptionNullableParDefaut(): void
    {
        $track = new Tracks();

        $this->assertNull($track->getDescription());
    }

    public function testIdNullAvantPersistance(): void
    {
        $track = new Tracks();

        $this->assertNull($track->getId());
    }
}