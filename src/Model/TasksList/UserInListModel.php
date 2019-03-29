<?php

namespace App\Model\TasksList;

use Symfony\Component\Validator\Constraints as Assert;
use App\Document\User;

/**
 * Model for user that using in ListModel
 * 
 * Each the user object is one user with special permissions in the list.
 */
class UserInListModel
{
    // Constants for all existed permissions
    const NO_PERMISSION = 0;
    const OWNER = 1;
    const VIEW = 2;
    const EDIT = 3;

    /**
     * Id of user
     *
     * @var string
     * 
     * @Assert\Type("string")
     * @Assert\Length(max=60)
     */
    protected $id;

    /**
     * Username of user
     *
     * @var string
     * 
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=100)
     * @Assert\Regex("/^[A-Za-z0-9]+$/", message="Username should have lower and upper case letters, numbers.")
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * Permission for list, one of the constants
     *
     * @var int
     * 
     * @Assert\Type("int")
     * @Assert\NotNull
     */
    protected $permission;

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    //SETTERS

    public function setPermission(int $permission)
    {
        $this->permission = $permission;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function __construct(string $id = null, string $username = null, int $permission = self::NO_PERMISSION)
    {
        $this->id = $id;
        $this->username = $username;
        $this->permission = $permission;
    }
}
