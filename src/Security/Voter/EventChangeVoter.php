<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventChangeVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        //Le Voter intervient si l'attribut est 'EVENT_CHANGE'
        // et si le sujet (ce sur quoi on vérifie le droit) est une instance de Event
        return $attribute ==='EVENT_CHANGE'
            && $subject instanceof Event;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Event $subject */

        $user = $token->getUser();
        // Utilisateur non connecté = pas le droit de supprimer
        if (!$user instanceof UserInterface) {
            return false;
        }


        // Utilisateur auteur de l'événement = autorisé à supprimer son propre événement
        if ($user === $subject->getAuthor()) {
            return true;
        }

        // Aucun des cas précédents = pas le droit de supprimer
        return false;
    }

}
