<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use App\Annotation as App;

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
     * @App\DeserializeEntity(type="App\Entity\Person", idField="id", idGetter="getId", setter="setPerson")
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
    public function getPlayedName(): string
    {
        return $this->playedName;
    }

    /**
     * @param string $playedName
     */
    public function setPlayedName(string $playedName)
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
    public function setMovie(Movie $movie)
    {
        $this->movie = $movie;
    }
}
