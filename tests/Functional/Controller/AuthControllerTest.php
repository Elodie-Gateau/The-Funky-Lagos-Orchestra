<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;
    private User $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $this->adminUser = new User();
        $this->adminUser->setEmail('admin_auth_test@test.com');
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        $this->adminUser->setPassword($hasher->hashPassword($this->adminUser, 'password123'));
        $this->adminUser->setName('Admin');
        $this->adminUser->setSurname('Test');

        $this->em->persist($this->adminUser);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $user = $this->em->find(User::class, $this->adminUser->getId());
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }
        parent::tearDown();
    }

    public function testMeRetourne401SiNonAuthentifie(): void
    {
        $this->client->request('GET', '/api/me');

        $this->assertResponseStatusCodeSame(401);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($data['authenticated']);
    }

    public function testMeRetourneUtilisateurAuthentifie(): void
    {
        $this->client->loginUser($this->adminUser);
        $this->client->request('GET', '/api/me');

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['authenticated']);
        $this->assertSame('admin_auth_test@test.com', $data['email']);
        $this->assertContains('ROLE_ADMIN', $data['roles']);
        $this->assertSame('Test', $data['surname']);
    }

    public function testLoginAvecCredentielsValides(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'admin_auth_test@test.com', 'password' => 'password123'])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $data);
        $this->assertSame('admin_auth_test@test.com', $data['email']);
    }

    public function testLoginAvecMauvaisMotDePasse(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'admin_auth_test@test.com', 'password' => 'mauvais_mot_de_passe'])
        );

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginAvecEmailInconnu(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'inconnu@test.com', 'password' => 'password123'])
        );

        $this->assertResponseStatusCodeSame(401);
    }
}
