<?php

namespace Iotubby\Forms;

use Nette;
use Nette\Application\UI\Form;
use Tomaj\Form\Renderer\BootstrapRenderer;


class FormFactory
{
	use Nette\SmartObject;

	/**
	 * @return Form
	 */
	public function createForm(): Form
	{
        $form = new Form();
        $form->setRenderer(new BootstrapRenderer());
        return $form;
	}
}
