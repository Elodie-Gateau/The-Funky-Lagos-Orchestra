<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    #[Route('/api/email')]
    public function sendEmail(Request $request, MailerInterface $mailer, RateLimiterFactoryInterface $anonymousApiLimiter, ValidatorInterface $validator): JsonResponse
    {
        $limiter = $anonymousApiLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $data = json_decode($request->getContent(), true);
        $constraints = new Assert\Collection([
            'name'    => [
                new Assert\NotBlank(message: 'Le nom est requis'),
                new Assert\Length(max: 100, maxMessage: '100 caractères maximum'),
                new Assert\Regex(
                    pattern: '/^[\p{L}\p{N}\s\-\'.]+$/u',
                    message: 'Caractères non autorisés',
                ),
            ],
            'from'    => [
                new Assert\NotBlank(message: "L'email est requis"),
                new Assert\Email(message: 'Adresse email invalide'),
                new Assert\Regex(
                    pattern: '/[\r\n]/',
                    message: 'Caractères non autorisés',
                    match: false,
                ),
            ],
            'subject' => [
                new Assert\NotBlank(message: 'Le sujet est requis'),
                new Assert\Length(max: 200, maxMessage: '200 caractères maximum'),
                new Assert\Regex(
                    pattern: '/^[\p{L}\p{N}\s\-\'.]+$/u',
                    message: 'Caractères non autorisés',
                ),
            ],
            'message' => [
                new Assert\NotBlank(message: 'Le message est requis'),
                new Assert\Length(
                    min: 10, minMessage: '10 caractères minimum',
                    max: 2000, maxMessage: '2000 caractères maximum'
                )
            ],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $field = trim($violation->getPropertyPath(), '[]');
                $errors[$field] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], 422);
        }

        $data['name']    = strip_tags($data['name']);
        $data['subject'] = strip_tags($data['subject']);
        $data['message'] = strip_tags($data['message']);

        $email = (new Email())
            ->from('contact@thefunkylagosorchestra.com')
            ->to('contact@thefunkylagosorchestra.com')
            ->replyTo($data['from'])
            ->subject('[' . $data['subject'] . '] - ' . $data['name'])
            ->text('De : ' . $data['name'] . ' <' . $data['from'] . ">\n\n" . $data['message']);

        $mailer->send($email);

        return $this->json(['success' => true]);
    }
}
