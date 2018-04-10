<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default"})
     */
    private $id;

    /**
     * @var  string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"Default"})
     */
    private $url;

    public function getId()
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
