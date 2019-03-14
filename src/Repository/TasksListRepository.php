<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Document\User;
use App\Document\TasksList;


class TasksListRepository extends DocumentRepository
{
    /**
     * Find all lists is that user can view
     *
     * @param User $user
     * @return array
     */
    public function findViewLists(User $user)
    {
        return $this->createQueryBuilder()
            ->find()
            ->addOr(
                $this->createQueryBuilder()
                    ->expr()
                    ->field('createdUserId')
                    ->equals($user->getId()),
                $this->createQueryBuilder()
                    ->expr()
                    ->field('viewUserIds')
                    ->equals($user->getId()),
                $this->createQueryBuilder()
                    ->expr()
                    ->field('editUserIds')
                    ->equals($user->getId())
            )
            ->getQuery()
            ->execute();
    }

    /**
     * Find all lists is that user can edit
     *
     * @param User $user
     * @return array
     */
    public function findEditLists(User $user)
    {
        return $this->createQueryBuilder()
            ->find()
            ->addOr(
                $this->createQueryBuilder()
                    ->expr()
                    ->field('createdUserId')
                    ->equals($user->getId()),
                $this->createQueryBuilder()
                    ->expr()
                    ->field('editUserIds')
                    ->equals($user->getId())
            )
            ->getQuery()
            ->execute();
    }
}
