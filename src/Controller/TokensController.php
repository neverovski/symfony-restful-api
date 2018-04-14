<?php

namespace App\Controller;

use App\Security\TokenStorage;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 * @Version("v1")
 */
class TokensController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * UserController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTEncoderInterface $jwtEncoder
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $jwtEncoder,
        TokenStorage $tokenStorage
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Rest\View(statusCode=201)
     */
    public function postTokenAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('App:User')->findOneBy(['username' => $request->getUser()]);
        if (!$user) {
            throw new BadCredentialsException();
        }

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword());

        if (!$isPasswordValid) {
            throw new BadCredentialsException();
        }

        $token = $this->jwtEncoder->encode(
            [
                'username' => $user->getUsername(),
                'exp' => time() + 3600
            ]
        );

        $this->tokenStorage->isTokenValid(
            $user->getUsername(),
            $token
        );

        return new JsonResponse(['token' => $token]);
    }
}
