<?php

namespace Organizer\Bundle\CalendarBundle\Controller;

use Organizer\Bundle\CalendarBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Organizer\Bundle\RestBundle\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DefaultController
 * @package Organizer\Bundle\CalendarBundle\Controller
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * @return \Organizer\Bundle\TodoBundle\Repository\EventRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository('OrganizerCalendarBundle:Event');
    }

    /**
     * @return \Organizer\Bundle\TodoBundle\Model\Serializer
     */
    protected function getSerializer()
    {
        return $this->container->get('organizer.todo.service.serializer');
    }

    /**
     * @Route("/")
     * @Method("GET")
     */
    public function listAction()
    {
        $events =
            $this->getRepository()
            ->findBy([], ['startDate' => 'ASC']);

        return new JsonResponse($this->getSerializer()->serialize($events));
    }

    /**
     * @Route("/{eventId}", requirements={"eventId" = "\d+"})
     * @Method("GET")
     */
    public function getAction($eventId)
    {
        $event = $this->getRepository()->find($eventId);
        if(!$event) {
            return new Response('', 404);
        }

        return new JsonResponse($this->getSerializer()->serialize($event));
    }

    /**
     * @Route("/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $event = $this->getSerializer()->deserialize(
            $request->getContent(),
            'Organizer\Bundle\CalendarBundle\Entity\Event'
        );

        $errors = $this->validateEvent($event);
        if($errors !== null) {
            return new JsonResponse(json_encode($errors), 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);

        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not create event: '.$e->getMessage(), 500);
        }

        return new JsonResponse($this->getSerializer()->serialize($event));
    }

    /**
     * @Route("/{eventId}", requirements={"eventId" = "\d+"})
     * @Method("PUT")
     */
    public function putAction(Request $request, $eventId)
    {
        $event = $this->getRepository()->find($eventId);
        if(!$eventId) {
            return new Response('', 404);
        }

        $updatedEvent = $this->getSerializer()->deserialize(
            $request->getContent(),
            'Organizer\Bundle\CalendarBundle\Entity\Event'
        );

        $errors = $this->validateEvent($updatedEvent);
        if($errors !== null) {
            return new JsonResponse(json_encode($errors), 400);
        }

        $event->setTitle($updatedEvent->getTitle());
        $event->setIsAllDay($updatedEvent->getIsAllDay());
        $event->setStartDate($updatedEvent->getStartDate());
        $event->setEndDate($updatedEvent->getEndDate());

        $em = $this->getDoctrine()->getManager();
        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not update event', 500);
        }

        return new JsonResponse($this->getSerializer()->serialize($event));
    }

    /**
     * @Route("/{eventId}", requirements={"eventId" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction($eventId)
    {
        $event = $this->getRepository()->find($eventId);
        if(!$event) {
            return new Response('', 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);

        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not remove event', 500);
        }

        return new Response('', 200);
    }

    /**
     * @param Event $event
     * @return array|null
     */
    private function validateEvent(Event $event)
    {
        $validator = $this->get('validator');
        $violationList = $validator->validate($event);

        if($violationList->count()) {
            $errors = ['errors' => []];
            foreach($violationList->getIterator() as $violation) {
                $errors['errors'][$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $errors;
        }

        return null;
    }

}
