<?php

namespace App\Controller\Api;

use App\Entity\Setting;
use App\Enum\SettingName;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/api')]
final class SettingController extends AbstractController
{
    #[Route('/settings', methods: ['GET'])]
    public function getSettings(SettingRepository $settingRepository): JsonResponse
    {
        $settings = $settingRepository->findAll();
        foreach ($settings as $setting) {
            switch ($setting->getName()) {
                case SettingName::Photo:
                    $image = $setting->getImage() ? '/uploads/' . $setting->getImage() : null;
                    break;
                case SettingName::Description:
                    $descriptionFr = $setting->getDescriptionFr() ?? null;
                    $descriptionEn = $setting->getDescriptionEn() ?? $setting->getDescriptionFr();
                    break;
                case SettingName::Email:
                    $email = $setting->getLink() ?? null;
                    break;
                case SettingName::Phone:
                    $phone = $setting->getContent() ?? null;
                    break;
                case SettingName::Facebook:
                    $facebook = $setting->getLink() ?? null;
                    break;
                case SettingName::Instagram:
                    $instagram = $setting->getLink() ?? null;
                    break;
                case SettingName::YouTube:
                    $youtube = $setting->getLink() ?? null;
                    break;
                case SettingName::SoundCloud:
                    $soundcloud = $setting->getLink() ?? null;
                    break;
            }
        }

        return $this->json([
            'image' => $image,
            'descriptions' => [
                'description_fr' => $descriptionFr,
                'description_en' => $descriptionEn
            ],
            'email' => $email,
            'phone' => $phone,
            'facebook' => $facebook,
            'instagram' => $instagram,
            'youtube' => $youtube,
            'soundcloud' => $soundcloud
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/settings/image', methods: ['POST'])]
    public function updateSettingsImage(
        Request $request,
        SettingRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $setting = $repo->findOneBy(['name' => SettingName::Photo]) ?? new Setting();

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
        $file = $request->files->get('imageFile');

        if ($file) {
            $filename = uniqid() . '.' . $file->guessExtension();
            $file->move($this->getParameter('uploads_dir'), $filename);

            if ($setting->getImage()) {
                $oldPath = $this->getParameter('uploads_dir') . '/' . $setting->getImage();
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $setting->setImage($filename);
            $setting->setUpdatedAt(new \DateTimeImmutable());
            $setting->setUpdatedBy($this->getUser());
        }

        $em->persist($setting);
        $em->flush();

        return $this->json(['success' => true, 'image' => '/uploads/' . $setting->getImage()]);
        }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/settings/descriptions', methods: ['POST'])]
    public function updateSettingsDescriptions(
        Request $request,
        SettingRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $setting = $repo->findOneBy(['name' => SettingName::Description]) ?? new Setting();
        $setting->setDescriptionFr($data['description_fr'] ?? null);
        $setting->setDescriptionEn($data['description_en'] ?? null);
        $setting->setUpdatedAt(new \DateTimeImmutable());
        $setting->setUpdatedBy($this->getUser());

        $em->persist($setting);
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/settings/contacts', methods: ['POST'])]
    public function updateSettingsContacts(
        Request $request,
        SettingRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $settingsMap = [
            'phone' => ['name' => SettingName::Phone, 'method' => 'setContent'],
            'email' => ['name' => SettingName::Email, 'method' => 'setLink'],
            'facebook' => ['name' => SettingName::Facebook, 'method' => 'setLink'],
            'instagram' => ['name' => SettingName::Instagram, 'method' => 'setLink'],
            'youtube' => ['name' => SettingName::YouTube, 'method' => 'setLink'],
            'soundcloud' => ['name' => SettingName::SoundCloud, 'method' => 'setLink'],
        ];

        foreach ($settingsMap as $field => $config) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $this->updateSetting(
                    $repo,
                    $em,
                    $config['name'],
                    $data[$field],
                    $config['method']
                );
            }
        }

        $em->flush();

        return $this->json(['success' => true]);
    }

    private function updateSetting(
        SettingRepository $repo,
        EntityManagerInterface $em,
        SettingName $settingName,
        string $value,
        string $method
    ): void {
        $setting = $repo->findOneBy(['name' => $settingName]);

        if ($setting) {
            $setting->$method($value);
            $setting->setUpdatedAt(new \DateTimeImmutable());
            $setting->setUpdatedBy($this->getUser());
            $em->persist($setting);
        }
    }
}
