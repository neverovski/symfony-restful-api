<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const SHOW = 'show';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * UserVoter constructor.
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::SHOW])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, [User::ROLE_ADMIN])) {
            return true;
        }
        switch ($attribute) {
            case self::SHOW:
                return $this->isUserHimself($subject, $token);
        }

        return false;
    }

    /**
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function isUserHimself($subject, TokenInterface $token): bool
    {
        $authenticateUser = $token->getUser();

        if (!$authenticateUser instanceof User) {
            return false;
        }

        /** @var User $user */
        $user = $subject;

        return $authenticateUser->getId() === $user->getId();
    }
}