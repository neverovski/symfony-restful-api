<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $movie = new Movie();
        $movie->setTitle('Green Mile');
        $movie->setYear(1999);
        $movie->setTime(189);
        $movie->setDescription('Jun');

        $manager->persist($movie);
        $manager->flush();
    }
}