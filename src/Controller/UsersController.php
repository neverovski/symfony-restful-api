<?php

namespace App\Controller;

use App\Security\TokenStorage;
use App\Entity\EntityMerger;
use App\Entity\User;
use App\Exception\ValidationException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Security("is_anonymous() or is_authenticated()")
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
     * @Security("is_granted('show', theUser)", message="Access denied")
     */
    public function getUserAction(?User $theUser)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }

        return $theUser;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body",
     *     options={"deserializationContext"={"groups"={"Deserialize"}}}
     * )
     * @Rest\NoRoute()
     */
    public function postUserAction(User $user, ConstraintViolationListInterface $validationErrors)
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
     * @Rest\NoRoute()
     * @ParamConverter(
     *     "modifiedUser",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"Patch"}},
     *         "deserializationContext"={"groups"={"Deserialize"}}
     *     }
     * )
     * @Security("is_granted('edit', theUser)", message="Access denied")
     */
    public function patchUserAction(?User $theUser, User $modifiedUser, ConstraintViolationListInterface $validationErrors)
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