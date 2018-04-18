<?php

namespace App\Controller;

use App\Security\TokenStorage;
use App\Entity\EntityMerger;
use App\Entity\User;
use App\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations\Version;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * @Security("is_anonymous() or is_authenticated()")
 * @Version("v1")
 */
class UsersController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;
    /**
     * @var EntityMerger
     */
    private $entityMerger;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * UserController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTEncoderInterface $jwtEncoder
     * @param TokenStorage $tokenStorage
     * @param EntityMerger $entityMerger
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $jwtEncoder,
        TokenStorage $tokenStorage,
        EntityMerger $entityMerger
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->entityMerger = $entityMerger;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{theUser}", name="get_user")
     * @Security("is_granted('show', theUser)", message="Access denied")
     * @SWG\Get(
     *     tags={"User"},
     *     summary="Get the user",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param User|null $theUser
     * @return User|null
     */
    public function getUsers(?User $theUser)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }

        return $theUser;
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\Post("/users", name="post_user")
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body",
     *     options={"deserializationContext"={"groups"={"Deserialize"}}}
     * )
     * @SWG\Post(
     *     tags={"User"},
     *     summary="Add a new user resource",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param User $user
     * @param ConstraintViolationListInterface $validationErrors
     * @return User
     */
    public function postUser(User $user, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $this->encodePassword($user);
        $user->setRoles([User::ROLE_USER]);
        $this->persistUser($user);

        return $user;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{theUser}", name="put_user")
     * @ParamConverter(
     *     "modifiedUser",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"Patch"}},
     *         "deserializationContext"={"groups"={"Deserialize"}}
     *     }
     * )
     * @Security("is_granted('edit', theUser)", message="Access denied")
     * @SWG\Put(
     *     tags={"User"},
     *     summary="Edit the user",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="Returned when successful"),
     *     @SWG\Response(response="404", description="Returned when movie is not found")
     * )
     *
     * @param User|null $theUser
     * @param User $modifiedUser
     * @param ConstraintViolationListInterface $validationErrors
     * @return User|null
     */
    public function putUser(?User $theUser, User $modifiedUser, ConstraintViolationListInterface $validationErrors)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        if (empty($modifiedUser->getPassword())) {
            $modifiedUser->setPassword(null);
        }
        $this->entityMerger->merge($theUser, $modifiedUser);

        $this->encodePassword($theUser);
        $this->persistUser($theUser);

        if ($modifiedUser->getPassword()) {
            $this->tokenStorage->invalidateToken($theUser->getUsername());
        }

        return $theUser;
    }

    /**
     * @param User $user
     */
    protected function encodePassword(User $user): void
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            )
        );
    }

    /**
     * @param User $user
     */
    protected function persistUser(User $user): void
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
    }
}