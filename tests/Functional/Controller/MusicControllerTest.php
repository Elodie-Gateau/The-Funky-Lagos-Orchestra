<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Tracks;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MusicControllerTest extends WebTestCase
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
        $this->adminUser->setEmail('admin_music_test@test.com');
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        $this->adminUser->setPassword($hasher->hashPassword($this->adminUser, 'password123'));
        $this->adminUser->setName('Admin');
        $this->adminUser->setSurname('MusicTest');

        $this->em->persist($this->adminUser);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->getRepository(Tracks::class)->createQueryBuilder('t')
            ->delete()
            ->where('t.title LIKE :title')
            ->setParameter('title', '%Test%')
            ->getQuery()
            ->execute();

        $user = $this->em->find(User::class, $this->adminUser->getId());
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }

        parent::tearDown();
    }

    public function testGetTracksRetourneTableau(): void
    {
        $this->client->request('GET', '/api/tracks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tracks', $data);
        $this->assertIsArray($data['tracks']);
    }

    public function testGetVisibleTracksRetourneSeulementLesVisibles(): void
    {
        $trackVisible = new Tracks();
        $trackVisible->setTitle('Track Test Visible FT');
        $trackVisible->setArtist('Artiste Test');
        $trackVisible->setAlbum('Album Test');
        $trackVisible->setDuration('3:00');
        $trackVisible->setAudioFile('/audio/test_visible.mp3');
        $trackVisible->setStatus('Publié');
        $trackVisible->setIsVisible(true);
        $this->em->persist($trackVisible);

        $trackInvisible = new Tracks();
        $trackInvisible->setTitle('Track Test Invisible FT');
        $trackInvisible->setArtist('Artiste Test');
        $trackInvisible->setAlbum('Album Test');
        $trackInvisible->setDuration('2:30');
        $trackInvisible->setAudioFile('/audio/test_invisible.mp3');
        $trackInvisible->setStatus('Brouillon');
        $trackInvisible->setIsVisible(false);
        $this->em->persist($trackInvisible);

        $this->em->flush();

        $this->client->request('GET', '/api/tracks/home');

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('tracks', $data);

        $titles = array_column($data['tracks'], 'title');
        $this->assertContains('Track Test Visible FT', $titles);
        $this->assertNotContains('Track Test Invisible FT', $titles);
    }

    public function testAddTrackSansAuthentificationRetourne401(): void
    {
        $this->client->request('POST', '/api/admin/tracks/add');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddTrackSansFichierAudioRetourne400(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/tracks/add', [
            'title' => 'Track Test Sans Audio',
            'artist' => 'Artiste',
            'album' => 'Album',
            'duration' => '3:00',
            'status' => 'Brouillon',
            'isVisible' => 'false',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Fichier audio manquant', $data['error']);
    }

    public function testUpdateTrackSansAuthentificationRetourne401(): void
    {
        $track = new Tracks();
        $track->setTitle('Track Test Update Non Auth FT');
        $track->setArtist('Artiste Test');
        $track->setAlbum('Album Test');
        $track->setDuration('3:00');
        $track->setAudioFile('/audio/test.mp3');
        $track->setStatus('Brouillon');
        $track->setIsVisible(false);
        $this->em->persist($track);
        $this->em->flush();

        $this->client->request('POST', '/api/admin/tracks/' . $track->getId());

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateTrackAvecStatutBrouillonEtVisibleRetourne400(): void
    {
        $track = new Tracks();
        $track->setTitle('Track Test Statut Brouillon Visible FT');
        $track->setArtist('Artiste Test');
        $track->setAlbum('Album Test');
        $track->setDuration('3:00');
        $track->setAudioFile('/audio/test.mp3');
        $track->setStatus('Brouillon');
        $track->setIsVisible(false);
        $this->em->persist($track);
        $this->em->flush();

        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/tracks/' . $track->getId(), [
            'title' => 'Track Test Statut Brouillon Visible FT',
            'artist' => 'Artiste',
            'album' => 'Album',
            'duration' => '3:00',
            'status' => 'Brouillon',
            'isVisible' => 'true',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('publié', strtolower($data['error']));
    }

    public function testUpdateTrackAvecTropDeVisibles(): void
    {
        // S'assure qu'il y a exactement 6 morceaux visibles
        $this->em->getRepository(Tracks::class)->createQueryBuilder('t')
            ->delete()
            ->where('t.title LIKE :title')
            ->setParameter('title', '%Test%')
            ->getQuery()
            ->execute();

        for ($i = 1; $i <= 6; $i++) {
            $t = new Tracks();
            $t->setTitle("Track Test Visible Remplissage $i FT");
            $t->setArtist('Artiste');
            $t->setAlbum('Album');
            $t->setDuration('3:00');
            $t->setAudioFile("/audio/fill_$i.mp3");
            $t->setStatus('Publié');
            $t->setIsVisible(true);
            $this->em->persist($t);
        }

        $track = new Tracks();
        $track->setTitle('Track Test Limite Visibilite FT');
        $track->setArtist('Artiste Test');
        $track->setAlbum('Album Test');
        $track->setDuration('3:00');
        $track->setAudioFile('/audio/test_update.mp3');
        $track->setStatus('Brouillon');
        $track->setIsVisible(false);
        $this->em->persist($track);
        $this->em->flush();

        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/tracks/' . $track->getId(), [
            'title' => 'Track Test Limite Visibilite FT',
            'artist' => 'Artiste',
            'album' => 'Album',
            'duration' => '3:00',
            'status' => 'Publié',
            'isVisible' => 'true',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('6', $data['error']);
    }
}
