<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Eleve;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Eleves;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation as Doc;

class EleveController extends FOSRestController
{
    /**
     * @Rest\Get("/eleves", name="app_eleve_list")
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
     *     description="Max number of eleves per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="Eleve",
     *     resource=true,
     *     description="Récupérer la liste de toutes les élèves."
     * )     
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Eleve')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Eleves($pager);
    }

    /**
     * @Rest\Get(
     *     path = "/eleves/{id}",
     *     name = "app_eleve_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @Doc\ApiDoc(
     *     section="Eleve",
     *     resource=true,
     *     description="Récupérer un élève depuis son identifiant",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de l'élève"
     *         }
     *     }
     * )          
     */
    public function showAction(Eleve $eleve)
    {
        return $eleve;
    }

    /**
     * @Rest\Post("/eleves")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("eleve", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Eleve",
     *     resource=true,
     *     description="Création d'un élève."
     * )          
     */
    public function createAction(Eleve $eleve, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }
        //$logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        //$logger->info('Eleve is ' . serialize($eleve));
        $notes = $eleve->getNote();
        foreach ($notes as $note) {
            $note->setEleve($eleve);
            $em->persist($eleve);
            $em->persist($note);
        }
        $em->flush();

        return $eleve;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/eleves/{id}",
     *     name = "app_eleve_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newEleve", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Eleve",
     *     resource=true,
     *     description="Mise à jour d'un élève ",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de l'élève"
     *         }
     *     }
     * )          
     */
    public function updateAction(Eleve $eleve, Eleve $newEleve, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $eleve->setNom($newEleve->getNom());
        $eleve->setPrenom($newEleve->getPrenom());
        //$eleve->setDateDeNaissance($newEleve->getDateDeNaissance());

        $this->getDoctrine()->getManager()->flush();

        return $eleve;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/eleves/{id}",
     *     name = "app_eleve_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     section="Eleve",
     *     resource=true,
     *     description="Suppression d'un élève",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="L'identifiant unique de l'élève"
     *         }
     *     }
     * )          
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        /** @var Eleve $eleve */
        $eleve = $em->getRepository('AppBundle:Eleve')->findOneById($id);
        if ($eleve){
            $em->remove($eleve);
            $em->flush();
        }
    }
}
