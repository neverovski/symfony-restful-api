<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("username", groups={"Default", "Patch"})
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least ane digit, one upper case and one lower case letter",
     *     groups={"Default", "Patch"}
     * )
     * @Serializer\Groups({"Deserialize"})
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"Default", "Patch"}
     * )
     * @Serializer\Type("string")
     * @Serializer\Groups({"Deserialize"})
     */
    private $retypedPassword;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", length=200)
     * @Serializer\Exclude()
     */
    private $roles;

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $retypedPassword
     */
    public function setRetypedPassword(?string $retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {

    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * @return int $id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }
}
