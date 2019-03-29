<?php

namespace App\Model\TasksList;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\TasksList;
use App\Document\User;

/**
 * Model of list with special object for users
 */
class ListModel
{
    /**
     * Id of list. It has to be from document TasksList
     *
     * @var string
     * 
     * @Assert\Type("string")
     * @Assert\Length(max=60)
     */
    protected $id;

    /**
     * Title of list. It has to be from document TasksList
     *
     * @var string
     * 
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=255)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * User that created this list. It can not change
     *
     * @var UserInListModel
     * 
     * @Assert\Valid
     */
    protected $createdUser;

    /**
     * All users that is in the list. Each user is special model
     *
     * @var UserInListModel[]
     * 
     * @Assert\Valid
     */
    protected $users = [];

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreatedUser()
    {
        return $this->createdUser;
    }

    public function getUsers()
    {
        return $this->users;
    }

    // SETTERS

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setUsers(array $users)
    {
        $this->users = $users;
    }

    public function __construct(string $id = null, string $title = null, UserInListModel $createdUser = null, array $users = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->createdUser = $createdUser;
        $this->users = $users;
    }

    /**
     * Save changes to db
     *
     * @return void
     */
    public function save(DocumentManager $dm)
    {
        $list = $dm
            ->getRepository(TasksList::class)
            ->findOneById($this->id);

        $userRespository = $dm->getRepository(User::class);

        // Change title and save
        $list->setTitle($this->title);

        // Preparation user fields for save
        $owner = $this->createdUser->getId();
        $viewUserIds = [];
        $editUserIds = [];
        foreach ($this->users as $user) {
            switch ($user->getPermission()) {
                case UserInListModel::VIEW:
                    if (is_null($user->getId())) {
                        $userDocument = $userRespository->findOneByUsername($user->getUsername());
                        $user = new UserInListModel($userDocument->getId(), $userDocument->getUsername(), $user->getPermission());
                    }
                    $viewUserIds[] = $user->getId();
                    break;
                case UserInListModel::EDIT:
                    if (is_null($user->getId())) {
                        $userDocument = $userRespository->findOneByUsername($user->getUsername());
                        $user = new UserInListModel($userDocument->getId(), $userDocument->getUsername(), $user->getPermission());
                    }
                    $editUserIds[] = $user->getId();
                    break;
                default:
                    break;
            }
        }

        // Save users in the appropriate fields
        $list->setCreatedUserId($owner);
        $list->setViewUserIds($viewUserIds);
        $list->setEditUserIds($editUserIds);

        // Save all changes
        $dm->persist($list);
    }
}
