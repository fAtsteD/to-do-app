<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * List of tasks. All tasks have to be belonged to lists.
 * 
 * @ODM\Document(repositoryClass="App\Repository\TasksListRepository")
 * @ODM\UniqueIndex(keys={"userId", "taskId"})
 */
class TasksList
{
    /**
     * ID of list
     * 
     * @var string $id 
     * 
     * @ODM\Id
    */
    protected $id;

    /**
     * Name of list
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=255)
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * Id of user is created the list
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     */
    protected $createdUserId;

    /**
     * Id of user view the list
     *
     * @var array
     * 
     * @ODM\Field(type="collection")
     */
    protected $viewUserIds = [];

    /**
     * Id of user can edit the list
     *
     * @var array
     * 
     * @ODM\Field(type="collection")
     */
    protected $editUserIds = [];

    /**
     * Date when task has been created.
     *
     * @var \DateTime
     *
     * @ODM\Field(type="date")
     */
    protected $createdAt;

    /**
     * Define time of creating object for createdAt field.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }

    public function getViewUserIds()
    {
        return $this->viewUserIds;
    }

    public function getEditUserIds()
    {
        return $this->editUserIds;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // SETTERS

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setCreatedUserId(string $userId)
    {
        $this->createdUserId = $userId;
    }

    public function setViewUserIds(array $userIds)
    {
        $this->viewUserIds = $userIds;
    }

    public function setEditUserIds(array $userIds)
    {
        $this->editUserIds = $userIds;
    }
}
