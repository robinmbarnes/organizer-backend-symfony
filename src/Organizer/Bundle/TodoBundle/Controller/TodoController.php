<?php

namespace Organizer\Bundle\TodoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Organizer\Bundle\RestBundle\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TodoController
 * @package Organizer\Bundle\TodoBundle\Controller
 * @Route("/todo")
 */
class TodoController extends Controller
{

    /**
     * @return \Organizer\Bundle\TodoBundle\Repository\TodoRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository('OrganizerTodoBundle:Todo');
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
        $incompleteTodos = $this->getRepository()->findAllIncomplete();

        return new JsonResponse($this->getSerializer()->serialize($incompleteTodos));
    }

    /**
     * @Route("/{todoId}", requirements={"todoId" = "\d+"})
     * @Method("GET")
     */
    public function getAction($todoId)
    {
        $todo = $this->getRepository()->find($todoId);
        if(!$todo) {
            return new Response('', 404);
        }

        return new JsonResponse($this->getSerializer()->serialize($todo));
    }

    /**
     * @Route("/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $todo = $this->getSerializer()->deserialize(
            $request->getContent(),
            'Organizer\Bundle\TodoBundle\Entity\Todo'
        );

        $todo->setCreatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);

        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not create item: '.$e->getMessage(), 500);
        }

        return new JsonResponse($this->getSerializer()->serialize($todo));
    }

    /**
     * @Route("/{todoId}", requirements={"todoId" = "\d+"})
     * @Method("PUT")
     */
    public function putAction(Request $request, $todoId)
    {
        $todo = $this->getRepository()->find($todoId);
        if(!$todo) {
            return new Response('', 404);
        }

        $updatedTodo = $this->getSerializer()->deserialize(
            $request->getContent(),
            'Organizer\Bundle\TodoBundle\Entity\Todo'
        );

        if($updatedTodo->getIsComplete() && !$todo->getIsComplete()) {
            $todo->setCompletedAt(new \DateTime());
        } elseif(!$updatedTodo->getIsComplete() && $todo->getIsComplete()) {
            $todo->setIsComplete(false);
            $todo->setCompletedAt(null);
        }
        $todo->setTitle($updatedTodo->getTitle());

        $em = $this->getDoctrine()->getManager();
        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not update item', 500);
        }

        return new JsonResponse($this->getSerializer()->serialize($todo));
    }

    /**
     * @Route("/{todoId}", requirements={"todoId" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction($todoId)
    {
        $todo = $this->getRepository()->find($todoId);
        if(!$todo) {
            return new Response('', 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($todo);

        try {
            $em->flush();
        } catch(\Exception $e) {
            return new Response('Could not remove item', 500);
        }

        return new Response('', 200);
    }
}
