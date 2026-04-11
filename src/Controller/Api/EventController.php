<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class EventController extends AbstractController
{
    #[Route('/events', methods: ['GET'])]
    public function getEvents(EventRepository $eventRepository): JsonResponse
    {
        return $this->json([
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/events/home', methods: ['GET'])]
    public function getNextEvents(EventRepository $eventRepository): JsonResponse
    {
        $today = new \DateTime();
        $events = $eventRepository->findAllNext();
        return $this->json([
            'events' => $events,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/event/add', methods: ['POST'])]
    public function addEvent(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $event = new Event();
        $event->setName($request->request->get('name'));
        $event->setDate(new \DateTime($request->request->get('date')));
        $event->setLocation($request->request->get('location'));
        $event->setHost($request->request->get('host'));

        $em->persist($event);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/admin/event/{id}', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateEvent(Event $event, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $event->setName($request->request->get('name'));
        $event->setDate(new \DateTime($request->request->get('date')));
        $event->setLocation($request->request->get('location'));
        $event->setHost($request->request->get('host'));

        $em->persist($event);
        $em->flush();
        return $this->json(['success' => true]);
    }
}
