<?php
namespace Refactory\Login;

use TYPO3\Flow\Core\Booting\Step;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\Package as BasePackage;

/**
 * The Refactory Login Package
 */
class Package extends BasePackage {

	/**
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
	 * @return void
	 */
	public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		$dispatcher = $bootstrap->getSignalSlotDispatcher();

		$dispatcher->connect(
			'Refactory\Login\Controller\PasswordController', 'sendResetRequest',
			'Refactory\Notifications\Service\SlotService', 'dispatchNotifications'
		);
	}

}