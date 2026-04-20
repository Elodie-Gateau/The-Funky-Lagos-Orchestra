<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testEnvoiEmailAvecDonneesValides(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Jean Dupont',
                'from' => 'jean.dupont@example.com',
                'subject' => 'Demande de concert',
                'message' => 'Bonjour, je souhaite vous réserver pour un concert.',
            ])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testEnvoiEmailSansNomRetourne400(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'from' => 'jean.dupont@example.com',
                'subject' => 'Demande de concert',
                'message' => 'Bonjour.',
            ])
        );

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testEnvoiEmailSansEmailRetourne400(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Jean Dupont',
                'subject' => 'Demande de concert',
                'message' => 'Bonjour.',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testEnvoiEmailAvecEmailInvalideRetourne400(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Jean Dupont',
                'from' => 'email-invalide',
                'subject' => 'Sujet',
                'message' => 'Message.',
            ])
        );

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Invalid email address', $data['error']);
    }

    public function testEnvoiEmailSansSujetRetourne400(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Jean Dupont',
                'from' => 'jean@example.com',
                'message' => 'Bonjour.',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testEnvoiEmailSansMessageRetourne400(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Jean Dupont',
                'from' => 'jean@example.com',
                'subject' => 'Sujet',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testEnvoiEmailAvecCorpsJsonVide(): void
    {
        $this->client->request(
            'POST',
            '/api/email',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{}'
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
