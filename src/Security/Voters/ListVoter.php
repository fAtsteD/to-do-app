<?php

namespace App\Security\Voters;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use App\Document\TasksList;
use App\Document\User;

class ListVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @inheritDoc
     */
    public function supports($attribute, $subject)
    {
        // Apply only object of list
        if (!$subject instanceof TasksList) {
            return false;
        }

        // Attributes that it is accepted
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Get user object
        $user = $token->getUser();

        // User has to be logged in
        if (!$user instanceof User) {
            return false;
        }

        $list = $subject;

        // Check each attribute
        switch ($attribute) {
            case self::VIEW:
                return $this->isOwner($list, $user) ||
                    $this->canView($list, $user) ||
                    $this->canEdit($list, $user);
            case self::EDIT:
                return $this->isOwner($list, $user) ||
                    $this->canEdit($list, $user);
            case self::DELETE:
                return $this->isOwner($list, $user);
            default:
                return false;
        }
    }

    /**
     * Check for user owning the list
     * 
     * @param TasksList $list
     * @param User $user
     * @return bool
     */
    private function isOwner(TasksList $list, User $user)
    {
        return $user->getId() === $list->getCreatedUserId() ? true : false;
    }

    /**
     * Check for user in view array
     *
     * @param TasksList $list
     * @param User $user
     * @return bool
     */
    private function canView(TasksList $list, User $user)
    {
        return in_array($user->getId(), $list->getViewUserIds(), true);
    }

    /**
     * Check for user in edit array
     *
     * @param TasksList $list
     * @param User $user
     * @return bool
     */
    private function canEdit(TasksList $list, User $user)
    {
        return in_array($user->getId(), $list->getEditUserIds(), true);
    }

    /**
     * User cannot delete or edit special lists
     *
     * @param TasksList $list
     * @return bool
     */
    private function isListNotEditAndDelete(TasksList $list)
    {
        // Array of list that cannot delte or edit
        $cannotEditAndDelete = [
            'Inbox',
        ];

        return in_array($list->getTitle(), $cannotEditAndDelete, true);
    }
}
