<?php

namespace App\Controller;

use App\Controller\Pagination\Pagination;
use App\Entity\Movie;
use App\Entity\Role;
use App\Entity\EntityMerger;
use App\Repository\RoleRepository;
use App\Resource\Filtering\Movie\MovieFilterDefinitionFactory;
use App\Resource\Pagination\PageRequestFactory;
use FOS\RestBundle\Controller\ControllerTrait;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;
    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * MoviesController constructor.
     * @param EntityMerger $entityMerger
     * @param Pagination $pagination
     */
    public function __construct(EntityMerger $entityMerger, Pagination $pagination)
    {
        $this->entityMerger = $entityMerger;
        $this->pagination = $pagination;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction(Request $request)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $movieFilterDefinitionFactory = new MovieFilterDefinitionFactory();
        $movieFilterDefinition = $movieFilterDefinitionFactory->factory($request);
        dump($movieFilterDefinition); die;

        return $this->pagination->paginate(
            $request,
            'App:Movie',
            [],
            'findCount',
            [],
            'get_movies',
            []
        );
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $manager = $this->getDoctrine()->getManager();

        $manager->persist($movie);
        $manager->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function deleteMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($movie);
        $manager->flush();
    }

    /**
     * @Rest\View()
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function getMovieRolesAction(Request $request, Movie $movie)
    {
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);
        /** @var RoleRepository $repository */
        $repository = $this->getDoctrine()->getRepository('App:Role');
        $offset = ($page - 1) * $limit;
        $roles = $repository->findBy(
            ['movie' => $movie->getId()],
            [],
            $limit,
            $offset
        );
        $roleCount = $repository->getCountMovie($movie->getId());
        $pageCount = (int)ceil($roleCount / $limit);
        $collection = new CollectionRepresentation($roles);
        $paginated = new PaginatedRepresentation(
            $collection,
            'get_movie_roles',
            ['movie' => $movie->getId()],
            $page,
            $limit,
            $pageCount
        );
        return $this->pagination->paginate(
            $request,
            'App:Role',
            [],
            'getCountMovie',
            [$movie->getId()],
            'get_movie_roles',
            ['movie' => $movie->getId()]
        );
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"Deserialize"}}})
     * @Rest\NoRoute()
     */
    public function postMovieRolesAction(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $role->setMovie($movie);
        $manager = $this->getDoctrine()->getManager();

        $manager->persist($role);
        $movie->getRoles()->add($role);

        $manager->persist($movie);
        $manager->flush();

        return $role;
    }

    /**
     * @Rest\View()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body",
     *     options={"validator" = {"groups" = {"Patch"}}}
     * )
     * @Rest\NoRoute()
     * @Security("is_authenticated()")
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $this->entityMerger->merge($movie, $modifiedMovie);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($movie);
        $manager->flush();

        return $movie;
    }
}
