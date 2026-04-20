<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GalleryControllerTest extends WebTestCase
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
        $this->adminUser->setEmail('admin_gallery_test@test.com');
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        $this->adminUser->setPassword($hasher->hashPassword($this->adminUser, 'image/jpeg'));
        $this->adminUser->setName('Admin');
        $this->adminUser->setSurname('GalleryTest');

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

    public function testGetPhotosRetourneTableau(): void
    {
        $this->client->request('GET', '/api/photos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('photos', $data);
        $this->assertIsArray($data['photos']);
    }

    public function testAddPhotoSansAuthentificationRetourne401(): void
    {
        $this->client->request('POST', '/api/admin/photo/add');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddPhotoSansFichierRetourne400(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/api/admin/photo/add');

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testAddPhotoAvecFormatInvalideRetourne400(): void
    {
        $this->client->loginUser($this->adminUser);

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_photo_') . '.txt';
        file_put_contents($tmpFile, 'contenu texte invalide');

        $uploadedFile = new UploadedFile($tmpFile, 'test.txt', 'text/plain', null, true);

        $this->client->request('POST', '/api/admin/photo/add', [], ['image' => $uploadedFile]);

        $this->assertResponseStatusCodeSame(400);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Format non autorisé', $data['error']);

        @unlink($tmpFile);
    }

    public function testDeletePhotoSansAuthentificationRetourne401(): void
    {
        $this->client->request('DELETE', '/api/admin/photo/999/delete');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeletePhotoInexistanteRetourne404(): void
    {
        $this->client->loginUser($this->adminUser);

        $this->client->request('DELETE', '/api/admin/photo/999999/delete');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeletePhotoExistante(): void
    {
        $photo = new Photo();
        $photo->setPath('/images/gallery/test_delete_func.jpg');
        $this->em->persist($photo);
        $this->em->flush();

        $this->client->loginUser($this->adminUser);

        $photoId = $photo->getId();

        $this->client->request('DELETE', '/api/admin/photo/' . $photoId . '/delete');

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);

        $this->em->clear();
        $deletedPhoto = $this->em->find(Photo::class, $photoId);
        $this->assertNull($deletedPhoto);
    }
}
