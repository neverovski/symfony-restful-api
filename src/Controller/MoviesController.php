<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Role;
use App\Entity\EntityMerger;
use App\Resource\Filtering\Movie\MovieFilterDefinitionFactory;
use App\Resource\Filtering\Role\RoleFilterDefinitionFactory;
use App\Resource\Pagination\Movie\MoviePagination;
use App\Resource\Pagination\PageRequestFactory;
use App\Resource\Pagination\Role\RolePagination;
use FOS\HttpCacheBundle\Configuration\InvalidateRoute;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\ControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;
use Swagger\Annotations as SWG;

/**
 * @Security("is_anonymous() or is_authenticated()")
 * @Version("v1")
 */
class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * @var MoviePagination
     */
    private $moviePagination;

    /**
     * @var RolePagination
     */
    private $rolePagination;

    /**
     * MoviesController constructor.
     * @param EntityMerger $entityMerger
     * @param MoviePagination $moviePagination
     * @param RolePagination $rolePagination
     */
    public function __construct(
        EntityMerger $entityMerger,
        MoviePagination $moviePagination,
        RolePagination $rolePagination
    )
    {
        $this->entityMerger = $entityMerger;
        $this->moviePagination = $moviePagination;
        $this->rolePagination = $rolePagination;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/movies", name="get_movies")
     * @SWG\Get(
     *     tags={"Movie"},
     *     summary="Gets the all movie",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Request $request
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function getMovies(Request $request)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $movieFilterDefinitionFactory = new MovieFilterDefinitionFactory();
        $movieFilterDefinition = $movieFilterDefinitionFactory->factory($request);

        return $this->moviePagination->paginate($page, $movieFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/movies", name="post_movies")
     * @ParamConverter("movie", converter="fos_rest.request_body")     *
     * @SWG\Post(
     *     tags={"Movie"},
     *     summary="Add a new movie resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="201", description="Returned when resource created", @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="400", description="Returned when invalid date posted"),
     *     @SWG\Response(response="401", description="Returned when not authenticated"),
     *     @SWG\Response(response="403", description="Returned when token is invalid or expired")
     * )
     *
     * @param Movie $movie
     * @param ConstraintViolationListInterface $validationErrors
     * @return Movie
     */
    public function postMovies(Movie $movie, ConstraintViolationListInterface $validationErrors)
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
     * @InvalidateRoute("get_movie", params={"movie" = {"expression" = "movie.getId()"}})
     * @InvalidateRoute("get_movies")
     * @Rest\Delete("/movies/{movie}", name="delete_movie")
     * @SWG\Delete(
     *     tags={"Movie"},
     *     summary="Delete the movie",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="movie", in="path", type="integer", description="Movie id", required=true),
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Movie|null $movie
     * @return \FOS\RestBundle\View\View
     */
    public function deleteMovie(?Movie $movie)
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
     * @Rest\Get("/movies/{movie}", name="get_movie")
     * @Cache(public=true, maxage=3600, smaxage=3600)
     * @SWG\Get(
     *     tags={"Movie"},
     *     summary="Gets the movie",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="movie", in="path", type="integer", description="Movie id", required=true),
     *     @SWG\Response(response="200", description="Returned when successful", @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param Movie|null $movie
     * @return Movie|\FOS\RestBundle\View\View|null
     */
    public function getMovie(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/movies/{movie}/roles", name="get_movie_roles")
     *
     * @param Request $request
     * @param Movie $movie
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function getMovieRoles(Request $request, Movie $movie)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $roleFilterDefinitionFactory = new RoleFilterDefinitionFactory();
        $roleFilterDefinition = $roleFilterDefinitionFactory->factory(
            $request,
            $movie->getId()
        );

        return $this->rolePagination->paginate($page, $roleFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/movies/{movie}/roles", name="post_movie_roles")
     * @ParamConverter("role", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"Deserialize"}}})
     *
     * @param Movie $movie
     * @param Role $role
     * @param ConstraintViolationListInterface $validationErrors
     * @return Role
     */
    public function postMovieRoles(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
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
     * @Rest\Put("/movies/{movie}", name="put_movie")
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body",
     *     options={"validator" = {"groups" = {"Patch"}}}
     * )
     * @Security("is_authenticated()")
     * @SWG\Put(
     *     tags={"Movie"},
     *     summary="Edit the movie",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="201", description="Returned when resource update", @SWG\Schema(type="array", @Model(type=Movie::class))),
     *     @SWG\Response(response="400", description="Returned when invalid date posted"),
     *     @SWG\Response(response="401", description="Returned when not authenticated"),
     *     @SWG\Response(response="403", description="Returned when token is invalid or expired")
     * )
     *
     * @param Movie|null $movie
     * @param Movie $modifiedMovie
     * @param ConstraintViolationListInterface $validationErrors
     * @return Movie|\FOS\RestBundle\View\View|null
     */
    public function putMovie(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
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
