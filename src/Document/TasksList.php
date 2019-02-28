<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * List of tasks. All tasks have to be belonged to lists.
 * 
 * @ODM\Document
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
     * Id of user is owned the list
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     */
    protected $userId;

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

    public function getUserId()
    {
        return $this->userId;
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

    public function setUserId(string $userId)
    {
        $this->userId = $userId;
    }
}
