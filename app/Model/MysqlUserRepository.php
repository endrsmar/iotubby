<?php

namespace Iotubby\Model;

use Iotubby\Model\Entities\User;
use Nette;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;


class MysqlUserRepository implements UserRepository
{

    /**
     * @var Context
     */
    private $database;

    /**
     * @param Context $database
     */
    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        $res = [];
        foreach ($this->database->table(User::TABLE_NAME) as $row)
        {
            $res[] = $this->rowToEntity($row);
        }
        return $res;
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUser(string $username): ?User
    {
        if (!($row = $this->database->table(User::TABLE_NAME)->where(User::COLUMN_USERNAME, $username)->fetch())) {
            return null;
        }
        return $this->rowToEntity($row);
    }

    /**
     * @param User $user
     * @throws DuplicateNameException
     */
    public function save(User $user): void
    {
        try {
            if ($user->getId()) {
                $this->database->table(User::TABLE_NAME)->where(User::COLUMN_ID, $user->getId())->update($user->toArray());
            } else {
                $row = $this->database->table(User::TABLE_NAME)->insert($user->toArray());
                $user->setId($row->id);
            }
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
        }
    }

    /**
     * @param User $user
     */
    public function delete(User $user): void
    {
        $this->database->table(User::TABLE_NAME)->where(User::COLUMN_ID, $user->getId())->delete();
    }

    /**
     * @param ActiveRow $row
     * @return User
     */
    public function rowToEntity(ActiveRow $row): User
    {
        return new User(
            $row->{User::COLUMN_ID},
            $row->{User::COLUMN_USERNAME},
            $row->{User::COLUMN_PASSWORD}
        );
    }
}



class DuplicateNameException extends \Exception
{
}
