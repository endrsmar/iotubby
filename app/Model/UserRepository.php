<?php
declare(strict_types=1);

namespace Iotubby\Model;


use Iotubby\Model\Entities\User;

interface UserRepository
{

    /**
     * @param string $username
     * @return User|null
     */
    public function getUser(string $username): ?User;

    /**
     * @return User[]
     */
    public function getUsers(): array;

    /**
     * @param User $user
     */
    public function save(User $user): void;

    /**
     * @param User $user
     */
    public function delete(User $user): void;

}