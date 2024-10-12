<?php

namespace App\Security\Voter;

use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute == Permission::EA_ACCESS_ENTITY) {
            return true;
        }
        return false;
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if($subject->getInstance() === null){
            return true;
        }   
        $user = $token->getUser();
        $entity = $subject->getInstance();
        if($user->getCompany() !== null){
            if(method_exists($entity, 'getCreatedBy')){
                if($entity->getCreatedBy()== null){
                    return true;
                }
                return  $user->getCompany() === $entity->getCreatedBy()->getCompany();
            }
        }
        if(method_exists($entity, 'getMembers')){
            return in_array($user, $entity->getMembers()->toArray());
        }
        return false;
    }
}
