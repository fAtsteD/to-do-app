<?php

namespace App\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class has user and standart implementation security.
 * 
 * @ODM\Document
 */
class User implements UserInterface
{
    /**
     * Roles in the app. Database has number of role.
     */
    const ROLE_USERS = [
        1 => 'ROLE_USER', // default role for all user, must have always
    ];

    /**
     * ID of user
     *
     * @var string
     * 
     * @ODM\Id
     */
    protected $id;

    /**
     * Name of user that has to be unique.
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=100)
     * @Assert\Regex("/^[A-Za-z0-9]+$/", message="Username should have lower and upper case letters, numbers.")
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * E-mail of user
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex
     * @Assert\Email
     * @Assert\NotBlank
     */
    protected $email;

    /**
     * Password of user that has to be hashed.
     *
     * @var string
     * 
     * @ODM\Field(type="string")
     * @Assert\Type("string")
     */
    protected $password;

    /**
     * Role of user
     *
     * @var int
     * 
     * @ODM\Field(type="int")
     */
    protected $role;

    /**
     * Birthday of user
     *
     * @var \DateTime
     * 
     * @ODM\Field(type="date")
     * @Assert\DateTime
     */
    protected $birthday;

    /**
     * Date is when user have been created
     *
     * @var \DateTime
     * 
     * @ODM\Field(type="date")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->role = array_search('ROLE_USER', self::ROLE_USERS);
    }

    public function eraseCredentials()
    { }

    // GETTERS

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return [self::ROLE_USERS[$this->role]];
    }

    // Using bcrypt. That method has to be return null.
    public function getSalt()
    {
        return null;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // SETTERS

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setRoles(string $role)
    {
        $this->role = array_search((in_array($role, self::ROLE_USERS) ? $role : 'ROLE_USER'), self::ROLE_USERS);
    }

    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = $birthday;
    }
}
