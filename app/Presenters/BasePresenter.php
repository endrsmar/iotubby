<?php

namespace Iotubby\Presenters;

use Iotubby\Components\UserPanel\UserPanel;
use Iotubby\Components\UserPanel\UserPanelFactory;
use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var UserPanelFactory @inject
     */
    public $userPanelFactory;

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() && !$this->isLinkCurrent('Sign:in')) {
            $this->redirect('Sign:in');
        }
    }

    /**
     * @return UserPanel
     */
    public function createComponentUserPanel(): UserPanel
    {
        return $this->userPanelFactory->create();
    }

}
