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
        $imageSetting = $settingRepository->findOneBy(['name' => SettingName::Photo]);
        $descriptions = $settingRepository->findOneBy(['name' => SettingName::Description]);
        return $this->json([
            'image' => $imageSetting?->getImage() ? '/uploads/' . $imageSetting->getImage() : null,
            'descriptions' => [
                'description_fr' => $descriptions?->getDescriptionFr(),
                'description_en' => $descriptions?->getDescriptionEn()
            ]
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
}
