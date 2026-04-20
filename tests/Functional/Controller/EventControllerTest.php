<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EventControllerTest extends WebTestCase
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
        $this->adminUser->setEmail('admin_event_test@test.com');
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        $this->adminUser->setPassword($hasher->hashPassword($this->adminUser, 'password123'));
        $this->adminUser->setName('Admin');
        $this->adminUser->setSurname('EventTest');

        $this->em->persist($this->adminUser);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->getRepository(Event::class)->createQueryBuilder('e')
            ->delete()
            ->where('e.name LIKE :name')
            ->setParameter('name', '%Test%')
            ->getQuery()
            ->execute();

        $user = $this->em->find(User::class, $this->adminUser->getId());
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }

        parent::tearDown();
    }

    public function testGetEventsRetourneTableau(): void
    {
        $this->client->request('GET', '/api/events');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('events', $data);
        $this->assertIsArray($data['events']);
    }

    public function testGetEventsHomeRetourneEvenementsFuturs(): void
    {
        $event = new Event();
        $event->setName('Concert Test Futur');
        $event->setDate(new \DateTime('+1 month'));
        $this->em->persist($event);

        $eventPasse = new Event();
        $eventPasse->setName('Concert Test Passe');
        $eventPasse->setDate(new \DateTime('-1 month'));
        $this->em->persist($eventPasse);

        $this->em->flush();

        $this->client->request('GET', '/api/events/home');

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('events', $data);

        $names = array_column($data['events'], 'name');
        $this->assertContains('Concert Test Futur', $names);
        $this->assertNotContains('Concert Test Passe', $names);
    }

    public function testAddEventSansAuthentificationRetourne401(): void
    {
        $this->client->request('POST', '/api/admin/event/add', [
            'name' => 'Test Event',
            'date' => '2026-12-01',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddEventAvecAdminCreeUnEvenement(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/event/add', [
            'name' => 'Concert Test Ajout',
            'date' => '2026-12-25',
            'location' => 'Salle Pleyel, Paris',
            'host' => 'Festival Jazz',
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);

        $event = $this->em->getRepository(Event::class)->findOneBy(['name' => 'Concert Test Ajout']);
        $this->assertNotNull($event);
        $this->assertSame('Salle Pleyel, Paris', $event->getLocation());
    }

    public function testAddEventAvecDateInvalideRetourne400(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/event/add', [
            'name' => 'Concert Test Invalid Date',
            'date' => 'pas-une-date',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testUpdateEventAvecAdmin(): void
    {
        $event = new Event();
        $event->setName('Concert Test Update Avant');
        $event->setDate(new \DateTime('2026-11-01'));
        $this->em->persist($event);
        $this->em->flush();

        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/event/' . $event->getId(), [
            'name' => 'Concert Test Update Après',
            'date' => '2026-11-15',
            'location' => 'Nouveau lieu',
            'host' => 'Nouvel organisateur',
        ]);

        $this->assertResponseIsSuccessful();

        $this->em->refresh($event);
        $this->assertSame('Concert Test Update Après', $event->getName());
        $this->assertSame('Nouveau lieu', $event->getLocation());
    }

    public function testUpdateEventSansAuthentificationRetourne401(): void
    {
        $event = new Event();
        $event->setName('Concert Test Update Non Auth');
        $event->setDate(new \DateTime('2026-11-01'));
        $this->em->persist($event);
        $this->em->flush();

        $this->client->request('POST', '/api/admin/event/' . $event->getId(), [
            'name' => 'Concert modifié',
            'date' => '2026-11-15',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
