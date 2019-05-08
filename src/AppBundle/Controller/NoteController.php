<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Notes;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation as Doc;

class NoteController extends FOSRestController
{
    /**
     * @Rest\Get("/notes", name="app_note_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of notes per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Note",
     *     resource=true,
     *     description="Récupérer la liste de toutes les notes."
     * )
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Note')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Notes($pager);
    }

    /**
     * @Rest\Get(
     *     path = "/notes/{id}",
     *     name = "app_note_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @Doc\ApiDoc(
     *     section="Note",
     *     resource=true,
     *     description="Récupérer une note depuis son identifiant",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de la note"
     *         }
     *     }
     * )     
     */
    public function showAction(Note $note)
    {
        return $note;
    }

    /**
     * @Rest\Post("/notes")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("note", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Note",
     *     resource=true,
     *     description="Création d'une note."
     * )     
     */
    public function createAction(Note $note, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($note);
        $em->flush();

        return $note;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/notes/{id}",
     *     name = "app_note_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newNote", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Note",
     *     resource=true,
     *     description="Mise à jour d'une note",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de la note"
     *         }
     *     }
     * )     
     */
    public function updateAction(Note $note, Note $newNote, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $note->setMatiere($newNote->getMatiere());
        $note->setEvaluation($newNote->getEvaluation());

        $this->getDoctrine()->getManager()->flush();

        return $note;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/notes/{id}",
     *     name = "app_note_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     section="Note",
     *     resource=true,
     *     description="Suppression d'une note",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de la note"
     *         }
     *     }
     * )     
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        /** @var Note $note */
        $note = $em->getRepository('AppBundle:Note')->findOneById($id);
        if ($note){
            $em->remove($note);
            $em->flush();
        }
    }
}
