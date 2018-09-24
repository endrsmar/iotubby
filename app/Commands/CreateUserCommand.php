<?php
declare(strict_types=1);

namespace Iotubby\Commands;


use Iotubby\Model\Entities\User;
use Iotubby\Model\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{


    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct(null);
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this->setName('user:create')
            ->setDescription('Creates user')
            ->addArgument('username', InputArgument::REQUIRED, 'username')
            ->addArgument('password', InputArgument::REQUIRED, 'password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_null(($username = $input->getArgument('username'))) || is_null(($password = $input->getArgument('password')))) {
            $output->writeln("Usage: user:create <username> <password>");
            return;
        }
        $this->userRepository->save((new User(null, $username, ''))->changePassword($password));
        $output->writeln("User {$username} successfully created");
    }

}