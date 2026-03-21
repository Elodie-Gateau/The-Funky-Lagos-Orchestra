<?php

namespace App\Controller\Api;

use App\Entity\Setting;
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
        $setting = $settingRepository->findOneBy([]) ?? new Setting();
        return $this->json([
            'image' => $setting->getImage()
            ? '/uploads/' . $setting->getImage()
                : null
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/settings', methods: ['POST'])]
    public function updateSettings(
        Request $request,
        SettingRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        $setting = $repo->findOneBy([]) ?? new Setting();

        $form = $this->createForm(SettingType::class, $setting);
        $form->submit([]);

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
        }

        $em->persist($setting);
        $em->flush();

        return $this->json(['success' => true, 'image' => '/uploads/' . $setting->getImage()]);
        }
}
