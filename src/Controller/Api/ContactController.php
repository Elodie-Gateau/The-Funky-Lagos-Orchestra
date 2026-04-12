<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/api/email')]
    public function sendEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? '';
        $from = $data['from'] ?? '';
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';

        if( empty($name) || empty($from) || empty($subject) || empty($message)) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
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
