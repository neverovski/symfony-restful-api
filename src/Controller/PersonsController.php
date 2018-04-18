<?php

namespace App\Controller;

use App\Entity\Person;
use App\Resource\Filtering\Person\PersonFilterDefinitionFactory;
use App\Resource\Pagination\PageRequestFactory;
use App\Resource\Pagination\Person\PersonPagination;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\ControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;
use Swagger\Annotations as SWG;

/**
 * Class HumansController
 * @Version("v1")
 */
class PersonsController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var PersonPagination
     */
    private $personPagination;

    /**
     * HumansController constructor.
     * @param PersonPagination $personPagination
     */
    public function __construct(PersonPagination $personPagination)
    {
        $this->personPagination = $personPagination;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/persons", name="get_persons")
     * @SWG\Get(
     *     tags={"Person"},
     *     summary="Gets the all person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Request $request
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function getPersons(Request $request)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $personFilterDefinitionFactory = new PersonFilterDefinitionFactory();
        $personFilterDefinition = $personFilterDefinitionFactory->factory($request);

        return $this->personPagination->paginate($page, $personFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/persons", name="post_persons")
     * @ParamConverter("person", converter="fos_rest.request_body")
     * @SWG\Post(
     *     tags={"Person"},
     *     summary="Add a new person resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Person $person
     * @param ConstraintViolationListInterface $validationErrors
     * @return Person
     */
    public function postPersons(Person $person, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $manager = $this->getDoctrine()->getManager();

        $manager->persist($person);
        $manager->flush();

        return $person;
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/persons/{person}", name="delete_person")
     * @SWG\Delete(
     *     tags={"Person"},
     *     summary="Delete the person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Person|null $person
     * @return \FOS\RestBundle\View\View
     */
    public function deletePerson(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($person);
        $manager->flush();
    }

    /**
     * @Rest\View()
     * @Rest\Get("/persons/{person}", name="get_person")
     * @SWG\Get(
     *     tags={"Person"},
     *     summary="Gets the person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Person|null $person
     * @return Person|\FOS\RestBundle\View\View|null
     */
    public function getPeron(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        return $person;
    }
}
