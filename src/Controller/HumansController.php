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
class HumansController extends AbstractController
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
     *
     * @SWG\Get(
     *     tags={"Person"},
     *     summary="Gets the all person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Person::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     */
    public function getHumansAction(Request $request)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $personFilterDefinitionFactory = new PersonFilterDefinitionFactory();
        $personFilterDefinition = $personFilterDefinitionFactory->factory($request);

        return $this->personPagination->paginate($page, $personFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/humans")
     * @ParamConverter("person", converter="fos_rest.request_body")
     *
     * @SWG\Post(
     *     tags={"Person"},
     *     summary="Add a new person resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(type="array", @Model(type=Person::class))),
     *     @SWG\Response(response="201", description="Returned when resource created", @SWG\Schema(type="array", @Model(type=Person::class))),
     *     @SWG\Response(response="400", description="Returned when invalid date posted"),
     *     @SWG\Response(response="401", description="Returned when not authenticated"),
     *     @SWG\Response(response="403", description="Returned when token is invalid or expired")
     * )
     */
    public function postHumansAction(Person $person, ConstraintViolationListInterface $validationErrors)
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
     *
     * @SWG\Delete(
     *     tags={"Person"},
     *     summary="Delete the person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="person", in="path", type="integer", description="Person id", required=true),
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Person::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     */
    public function deleteHumanAction(?Person $person)
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
     *
     * @SWG\Get(
     *     tags={"Person"},
     *     summary="Gets the person",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="person", in="path", type="integer", description="Person id", required=true),
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Person::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     */
    public function getHumanAction(?Person $person)
    {
        if (null === $person) {
            return $this->view(null, 404);
        }

        return $person;
    }
}
