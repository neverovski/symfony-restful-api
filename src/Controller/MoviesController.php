<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Role;
use App\Entity\EntityMerger;
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
     * MoviesController constructor.
     * @param EntityMerger $entityMerger
     */
    public function __construct(EntityMerger $entityMerger)
    {
        $this->entityMerger = $entityMerger;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction(Request $request)
    {
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);

        $offset = ($page - 1) * $limit;
        $repository = $this->getDoctrine()->getRepository('App:Movie');

        $movies = $repository->findBy([], [], $limit, $offset);
        $movieCount = $repository->findCount();

        $pageCount = (int)ceil($movieCount/$limit);

        $collection = new CollectionRepresentation($movies);
        $paginated = new PaginatedRepresentation(
            $collection,
            'get_movies',
            [],
            $page,
            $limit,
            $pageCount
        );
        return $paginated;
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
        $roles = $movie->getRoles();
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);

        $offset = ($page - 1) * $limit;
        $pageCount = (int)ceil(count($roles) / $limit);
        $collection = new CollectionRepresentation(array_splice($roles->toArray(), $offset, $limit));
        $paginated = new PaginatedRepresentation(
            $collection,
            'get_movie_roles',
            ['movie' => $movie->getId()],
            $page,
            $limit,
            $pageCount
        );
        return $paginated;
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
