<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Role;
use App\Entity\EntityMerger;
use Doctrine\Common\Annotations\Reader;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Exception\ValidationException;

class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository('App:Movie')->findAll();
        return $movies;
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
    public function getMovieRolesAction(Movie $movie)
    {
        return $movie->getRoles();
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
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }
        $this->entityMerger = new EntityMerger($this->reader);

        $this->entityMerger->merge($movie, $modifiedMovie);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($movie);
        $manager->flush();

        return $movie;
    }
}
