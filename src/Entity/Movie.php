<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\Collection;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @Hateoas\Relation(
 *      "roles",
 *      href = @Hateoas\Route(
 *          "get_movie_roles",
 *          parameters = {
 *              "movie" = "expr(object.getId())"
 *          }
 *      )
 * )
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Length(max=255)
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Range(min=1888, max=2025, groups={"Default", "Patch"})
     */
    private $year;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Range(min=1, max=300, groups={"Default", "Patch"})
     */
    private $time;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(groups={"Default"})
     */
    private $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Role", mappedBy="movie", cascade={"remove"})
     * @Serializer\Exclude()
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Movie
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return Movie
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return Movie
     */
    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Movie
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
