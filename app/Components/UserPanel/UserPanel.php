<?php
declare(strict_types=1);

namespace Iotubby\Components\UserPanel;


use Nette\Application\UI\Control;
use Nette\Security\User;

class UserPanel extends Control
{

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    public function render()
    {
        $this->template->setFile(__DIR__.'/UserPanel.latte');
        $this->template->user = $this->user->getIdentity();
        $this->template->render();
    }

}