<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testConstructeurInitialiseCreatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $event = new Event();
        $after = new \DateTimeImmutable();

        $this->assertNotNull($event->getCreatedAt());
        $this->assertGreaterThanOrEqual($before, $event->getCreatedAt());
        $this->assertLessThanOrEqual($after, $event->getCreatedAt());
    }

    public function testGetterSetterName(): void
    {
        $event = new Event();
        $event->setName('Concert au Zénith');

        $this->assertSame('Concert au Zénith', $event->getName());
    }

    public function testGetterSetterDate(): void
    {
        $event = new Event();
        $date = new \DateTime('2026-06-15');
        $event->setDate($date);

        $this->assertSame($date, $event->getDate());
    }

    public function testGetterSetterLocation(): void
    {
        $event = new Event();
        $event->setLocation('Paris, France');

        $this->assertSame('Paris, France', $event->getLocation());
    }

    public function testGetterSetterHost(): void
    {
        $event = new Event();
        $event->setHost('Festival de Jazz');

        $this->assertSame('Festival de Jazz', $event->getHost());
    }

    public function testLocationNullableParDefaut(): void
    {
        $event = new Event();

        $this->assertNull($event->getLocation());
    }

    public function testHostNullableParDefaut(): void
    {
        $event = new Event();

        $this->assertNull($event->getHost());
    }

    public function testIdNullAvantPersistance(): void
    {
        $event = new Event();

        $this->assertNull($event->getId());
    }

    public function testSetterRetourneInstance(): void
    {
        $event = new Event();

        $this->assertInstanceOf(Event::class, $event->setName('Test'));
        $this->assertInstanceOf(Event::class, $event->setDate(new \DateTime()));
        $this->assertInstanceOf(Event::class, $event->setLocation('Paris'));
        $this->assertInstanceOf(Event::class, $event->setHost('Host'));
    }
}
