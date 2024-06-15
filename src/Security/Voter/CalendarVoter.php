<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CalendarVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        $a = in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Calendar;
        return $a;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if($subject->getUser() === $user)
        {
            return true;
        }
        if($subject->getUser()->hasRole('ROLE_ADMIN'))
        {
            return true;
        }
        if($subject->getUser()->getManager()==$user)
        {
            return true;
        }

        return false;
    }
}
