<?php
namespace Refactory\Cmk\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Cmk".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

class ChangeController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('foos', array(
			'bar', 'baz'
		));
	}

}