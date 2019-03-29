<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Document\User;
use App\Document\TasksList;
use App\Model\TasksList\UserInListModel;
use App\Model\TasksList\ListModel;


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

    /**
     * Find list and return object of model of list
     *
     * @param string $listId
     * @return array|null
     */
    public function findListModel(string $listId)
    {
        $list = $this->findOneById($listId);

        // Check if list exist
        if (is_null($list)) {
            return null;
        }

        // Queary builder for User table for finding users
        $queryBuilder = $this->getDocumentManager()->createQueryBuilder(User::class);

        // Create array of expressions for query 
        $usersQuery[] = $queryBuilder
            ->expr()
            ->field('id')
            ->equals($list->getCreatedUserId());
        foreach ($list->getViewUserIds() as $userId) {
            $usersQuery[] = $queryBuilder
                ->expr()
                ->field('id')
                ->equals($userId);
        }
        foreach ($list->getEditUserIds() as $userId) {
            $usersQuery[] = $queryBuilder
                ->expr()
                ->field('id')
                ->equals($userId);
        }

        // Create query
        $query = $queryBuilder
            ->find()
            ->addOr(...$usersQuery)
            ->getQuery();

        // Create array users in list
        $users = $query->execute();
        $userInListModels = [];
        $userCreated = null;
        foreach ($users as $user) {
            if (in_array($user->getId(), $list->getViewUserIds(), true)) {
                $userInListModels[] = new UserInListModel($user->getId(), $user->getUsername(), UserInListModel::VIEW);
            } elseif (in_array($user->getId(), $list->getEditUserIds(), true)) {
                $userInListModels[] = new UserInListModel($user->getId(), $user->getUsername(), UserInListModel::EDIT);
            } elseif ($user->getId() === $list->getCreatedUserId()) {
                $userCreated = new UserInListModel($user->getId(), $user->getUsername(), UserInListModel::OWNER);
            }
        }

        return [
            $list,
            new ListModel($list->getId(), $list->getTitle(), $userCreated, $userInListModels),
        ];
    }
}
