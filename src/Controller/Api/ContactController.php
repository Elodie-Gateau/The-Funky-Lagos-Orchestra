<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/api/email')]
    public function sendEmail(Request $request, MailerInterface $mailer, RateLimiterFactoryInterface $anonymousApiLimiter): JsonResponse
    {
        $limiter = $anonymousApiLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? '';
        $from = $data['from'] ?? '';
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';

        if( empty($name) || empty($from) || empty($subject) || empty($message)) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Invalid email address'], Response::HTTP_BAD_REQUEST);
        }

        $email = (new Email())
            ->from('contact@thefunkylagosorchestra.com')
            ->to('contact@thefunkylagosorchestra.com')
            ->replyTo($from)
            ->subject("[$subject] - $name")
            ->text("De : $name <$from>\n\n$message");

        $mailer->send($email);

        return $this->json(['success' => true]);
    }
}
