<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPersonData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $perons1 = new Person();
        $perons1->setFirstName('Dmitry');
        $perons1->setLastName('Neverovski');
        $perons1->setDateOfBirth(new \DateTime('1994-03-09'));

        $manager->persist($perons1);
        $manager->flush();
    }
}