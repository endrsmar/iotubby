<?php
declare(strict_types=1);

namespace Iotubby\Commands;


use Iotubby\Model\Entities\User;
use Iotubby\Model\UserManager;
use Iotubby\Model\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteUserCommand extends Command
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
        $this->setName('user:delete')
            ->setDescription('deletes user')
            ->addArgument('username', InputArgument::REQUIRED, 'username');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_null(($username = $input->getArgument('username')))) {
            $output->writeln("Usage: user:create <username>");
            return;
        }
        if (!($user = $this->userRepository->getUser($username))) {
            throw new \InvalidArgumentException("User {$username} does not exist");
        }
        $this->userRepository->delete($user);
        $output->writeln("User {$username} successfully deleted");
    }

}