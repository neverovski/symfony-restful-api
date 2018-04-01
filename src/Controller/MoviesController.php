<?php

namespace App\Controller;

use App\Entity\Movie;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoviesController extends AbstractController
{
    use ControllerTrait;

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
    public function postMoviesAction(Movie $movie)
    {
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
        if(null === $movie) {
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
        if(null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }
}
