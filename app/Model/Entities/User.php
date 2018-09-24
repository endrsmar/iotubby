<?php
declare(strict_types=1);

namespace Iotubby\Model\Entities;


use Nette\Security\Passwords;

class User implements Entity
{

    public const TABLE_NAME = 'user',
                 COLUMN_ID = 'id',
                 COLUMN_USERNAME = 'username',
                 COLUMN_PASSWORD = 'password',
                 DEFAULT_ROLE = 'user';

    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @param int|null $id
     * @param string $username
     * @param string $password
     */
    public function __construct(?int $id, string $username, string $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param int $id
     * @return User
     * @throws \Exception
     */
    public function setId(int $id): User
    {
        if ($this->id !== null) {
            throw new \Exception('Entity already has id');
        }
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->password;
    }

    /**
     * @param string $newPassword
     * @return User
     */
    public function changePassword(string $newPassword): User
    {
        $this->password = Passwords::hash($newPassword);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::COLUMN_USERNAME => $this->username,
            self::COLUMN_PASSWORD => $this->password
        ];
    }

}