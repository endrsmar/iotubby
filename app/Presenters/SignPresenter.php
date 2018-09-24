<?php

namespace Iotubby\Presenters;

use Iotubby\Forms\SignInFormFactory;
use Nette\Application\UI\Form;


class SignPresenter extends BasePresenter
{
	/** @var SignInFormFactory */
	private $signInFactory;


	public function __construct(SignInFormFactory $signInFactory)
	{
        parent::__construct();
		$this->signInFactory = $signInFactory;
	}


	/**
	 * Sign-in form factory.
	 * @return Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->createSignInForm(function () {
			$this->redirect('Device:');
		});
	}


	public function actionOut()
	{
		$this->getUser()->logout();
        $this->redirect('Sign:in');
	}
}
