<?php

namespace Organizer\Bundle\CalendarBundle\Controller;

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
            ->findBy([], ['startDate' => 'ASC', 'startTime' => 'ASC']);

        return new JsonResponse($this->getSerializer()->serialize($events));
    }
}
