<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetRolesContientToujoursRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetRolesAvecRoleAdmin(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testGetRolesSansDoublons(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_USER']);

        $roles = $user->getRoles();

        $this->assertSame($roles, array_unique($roles));
    }

    public function testGetterSetterEmail(): void
    {
        $user = new User();
        $user->setEmail('admin@test.com');

        $this->assertSame('admin@test.com', $user->getEmail());
    }

    public function testGetUserIdentifierRetourneEmail(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function testGetterSetterName(): void
    {
        $user = new User();
        $user->setName('Jean-Pierre');

        $this->assertSame('Jean-Pierre', $user->getName());
    }

    public function testGetterSetterSurname(): void
    {
        $user = new User();
        $user->setSurname('Dupont');

        $this->assertSame('Dupont', $user->getSurname());
    }

    public function testGetterSetterPassword(): void
    {
        $user = new User();
        $user->setPassword('hashed_password');

        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testIdNullAvantPersistance(): void
    {
        $user = new User();

        $this->assertNull($user->getId());
    }

    public function testSerializeHashLeMotDePasse(): void
    {
        $user = new User();
        $user->setPassword('secret');

        $serialized = $user->__serialize();

        $passwordKey = "\0" . User::class . "\0password";
        $this->assertArrayHasKey($passwordKey, $serialized);
        $this->assertNotSame('secret', $serialized[$passwordKey]);
        $this->assertSame(hash('crc32c', 'secret'), $serialized[$passwordKey]);
    }
}
