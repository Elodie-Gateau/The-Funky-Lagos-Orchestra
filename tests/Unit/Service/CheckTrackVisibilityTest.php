<?php

namespace App\Tests\Unit\Service;

use App\Entity\Tracks;
use App\Repository\TracksRepository;
use App\Service\CheckTrackVisibility;
use PHPUnit\Framework\TestCase;

class CheckTrackVisibilityTest extends TestCase
{
    private TracksRepository $repository;
    private CheckTrackVisibility $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TracksRepository::class);
        $this->service = new CheckTrackVisibility($this->repository);
    }

    public function testRetourneErreurSiMorceauNonPublie(): void
    {
        $track = new Tracks();
        $track->setTitle('Test');
        $track->setStatus('Brouillon');

        $this->repository->expects($this->never())->method('count');

        $result = $this->service->checkVisibility($track);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('publié', $result['error']);
    }

    public function testRetourneErreurSiSixMorceauxVisiblesExistent(): void
    {
        $track = new Tracks();
        $track->setTitle('Test');
        $track->setStatus('Publié');
        $track->setIsVisible(true);

        $this->repository
            ->expects($this->once())
            ->method('count')
            ->with(['isVisible' => true])
            ->willReturn(6);

        $result = $this->service->checkVisibility($track);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('6', $result['error']);
    }

    public function testRetourneTableauVideSiPublieEtMoinsDesSixVisibles(): void
    {
        $track = new Tracks();
        $track->setTitle('Test');
        $track->setStatus('Publié');
        $track->setIsVisible(true);

        $this->repository
            ->expects($this->once())
            ->method('count')
            ->with(['isVisible' => true])
            ->willReturn(5);

        $result = $this->service->checkVisibility($track);

        $this->assertEmpty($result);
    }

    public function testPublieAvecZeroVisiblesExistants(): void
    {
        $track = new Tracks();
        $track->setTitle('Test');
        $track->setStatus('Publié');
        $track->setIsVisible(true);

        $this->repository->method('count')->willReturn(0);

        $result = $this->service->checkVisibility($track);

        $this->assertEmpty($result);
    }

    public function testLimiteExacteDesSixMorceauxVisibles(): void
    {
        $track = new Tracks();
        $track->setTitle('Test');
        $track->setStatus('Publié');
        $track->setIsVisible(true);

        $this->repository->method('count')->willReturn(6);

        $result = $this->service->checkVisibility($track);

        $this->assertArrayHasKey('error', $result);
    }
}
