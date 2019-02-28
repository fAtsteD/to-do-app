<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Document\Subtask;

/**
 * Class for each task.
 *
 * @ODM\Document
 */
class Task
{
    /**
     * ID of task
     *
     * @var string
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * True if task is done.
     *
     * @var bool
     *
     * @ODM\Field(type="boolean")
     * @Assert\Type("bool")
     */
    protected $isDone = false;

    /**
     * Name of task
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
     * Description of task, note for task.
     *
     * @var string
     *
     * @ODM\Field(type="string")
     * @Assert\Type("string")
     */
    protected $desctiption;

    /**
     * Tasks like array of strings. They can be parts of main tasks, subtask etc.
     *
     * @var Substask[]
     *
     * @ODM\EmbedMany(targetDocument=Subtask::class)
     * @Assert\Valid
     */
    protected $subtasks = [];

    /**
     * Date when task have to be done.
     *
     * @var \DateTime
     *
     * @ODM\Field(type="date")
     * @Assert\DateTime
     */
    protected $dueDate;

    /**
     * Id of the list has this task
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     */
    protected $listId;

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

    public function getIsDone()
    {
        return $this->isDone;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->desctiption;
    }

    public function getSubtasks()
    {
        return $this->subtasks;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function getListId()
    {
        return $this->listId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // SETTERS

    public function setIsDone(bool $isDone = false)
    {
        $this->isDone = $isDone;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setDescription(string $description)
    {
        $this->desctiption = $description;
    }

    public function setSubtasks(array $subtasks)
    {
        $this->subtasks = $subtasks;
    }

    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }

    public function setListId(string $listId)
    {
        $this->listId = $listId;
    }
}
