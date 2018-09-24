<?php
declare(strict_types=1);

namespace Iotubby\Services;


use Iotubby\Model\Entities\User;
use Iotubby\Model\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class UserAuthenticator implements IAuthenticator
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $credentials
     */
    function authenticate(array $credentials): IIdentity
    {
        list($username, $password) = $credentials;

        if (!($user = $this->userRepository->getUser($username)) || !Passwords::verify($password, $user->getPasswordHash())) {
            throw new AuthenticationException('The username or password is incorrect.', self::IDENTITY_NOT_FOUND);
        }
        if (Passwords::needsRehash($user->getPasswordHash())) {
            $user->changePassword($password);
            $this->userRepository->save($user);
        }
        $arr = $user->toArray();
        unset($arr[User::COLUMN_PASSWORD]);
        return new Identity($user->getId(), User::DEFAULT_ROLE, $arr);
    }
}