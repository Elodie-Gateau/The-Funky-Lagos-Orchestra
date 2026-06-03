<?php

namespace App\Controller\Api;

use App\Entity\Setting;
use App\Enum\SettingName;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api')]
final class SettingController extends AbstractController
{
    #[Route('/settings', methods: ['GET'])]
    public function getSettings(SettingRepository $settingRepository): JsonResponse
    {
        $settings = $settingRepository->findAll();
        $image = null;
        $descriptionEn = null;
        $descriptionFr = null;
        $email = null;
        $phone = null;
        $facebook = null;
        $instagram = null;
        $youtube = null;
        $soundcloud = null;
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

        if (!$setting->getName()) {
            $setting->setName(SettingName::Photo);
        }
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
        $file = $request->files->get('imageFile');

        if ($file) {
            $filename = bin2hex(random_bytes(16)) . '.' . $file->guessExtension();
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
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection(
            fields: [
                'description_fr' => new Assert\Optional([
                    new Assert\Length(max: 2000, maxMessage: '2000 caractères maximum'),
                ]),
                'description_en' => new Assert\Optional([
                    new Assert\Length(max: 2000, maxMessage: '2000 caractères maximum'),
                ]),
            ],
            allowExtraFields: true
        );

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $field = trim($violation->getPropertyPath(), '[]');
                $errors[$field] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], 422);
        }

        $setting = $repo->findOneBy(['name' => SettingName::Description]) ?? new Setting();
        if (!$setting->getName()) {
            $setting->setName(SettingName::Description);
        }
        $setting->setDescriptionFr(\strip_tags($data['description_fr'] ?? ''));
        $setting->setDescriptionEn(\strip_tags($data['description_en'] ?? ''));
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
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection(
            fields: [
                'phone' => new Assert\Optional([
                    new Assert\NotBlank(),
                    new Assert\Regex(
                        pattern: '/^0[1-9][0-9]{8}$/',
                        message: 'Numéro de téléphone invalide (format attendu: 0612345678).'
                    ),
                ]),
                'email' => new Assert\Optional([
                    new Assert\NotBlank(),
                    new Assert\Email(message: 'Adresse email invalide'),
                ]),
                'facebook'  => new Assert\Optional([$this->urlConstraint('facebook.com')]),
                'instagram' => new Assert\Optional([$this->urlConstraint('instagram.com')]),
                'youtube'   => new Assert\Optional([$this->urlConstraint('youtube.com')]),
                'soundcloud'=> new Assert\Optional([$this->urlConstraint('soundcloud.com')]),
            ],
            allowExtraFields: true
        );

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $field = trim($violation->getPropertyPath(), '[]');
                $errors[$field] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], 422);
        }

        if (isset($data['phone'])) {
            $data['phone'] = strip_tags($data['phone']);
        }
        if (isset($data['email'])) {
            $data['email'] = strip_tags($data['email']);
        }

        foreach (['facebook', 'instagram', 'youtube', 'soundcloud'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = strip_tags($data[$field]);
            }
        }

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
        $setting = $repo->findOneBy(['name' => $settingName]) ?? new Setting();

        if (!$setting->getName()) {
            $setting->setName($settingName);
        }
            $setting->$method($value);
            $setting->setUpdatedAt(new \DateTimeImmutable());
            $setting->setUpdatedBy($this->getUser());
            $em->persist($setting);
    }

    private function urlConstraint(string $domain): Assert\Regex
    {
        return new Assert\Regex(
            pattern: '/^https?:\/\/(www\.)?' . preg_quote($domain, '/') . '\/.+/',
            message: "L'URL doit être un profil $domain valide (ex: https://$domain/monprofil)",
        );
    }
}
