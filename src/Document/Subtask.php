<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class for subtasks in task.
 *
 * @ODM\EmbeddedDocument
 */
class Subtask
{
    /**
     * True if subtask is done.
     *
     * @var bool
     *
     * @ODM\Field(type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $isDone = false;

    /**
     * Name of subtask.
     *
     * @var string
     *
     * @ODM\Field(type="string")
     * @Assert\Type(type="string")
     * @Assert\Length(min=3, max=255)
     * @Assert\NotBlank
     */
    protected $title;

    // GETTERS

    public function getIsDone()
    {
        return $this->isDone;
    }

    public function getTitle()
    {
        return $this->title;
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
}
