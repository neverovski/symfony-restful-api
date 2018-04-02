<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person")
     */
    private $person;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=100)
     */
    private $playedName;

    /**
     * @var Movie
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="roles")
     */
    private $movie;

    /**
     * @return int id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person 
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person) 
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getlayedName(): string 
    {
        return $this->playedName;
    }

    /**
     * @param string $playedName
     */
    public function setlayedName(string $playedName) 
    {
        $this->playedName = $playedName;
    }    

    /**
     * @return Movie
     */
    public function getMovie(): Movie 
    {
        return $this->movie;
    }

    /**
     * @param Movie $movie
     */
    public function setMovien(Movie $movie) 
    {
        $this->movie = $movie;
    }
}
