<?php
namespace Refactory\Login;

use Neos\Flow\Package\Package as BasePackage;

/**
 * The Refactory Login Package
 */
class Package extends BasePackage
{

    /**
     * @param \Neos\Flow\Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(\Neos\Flow\Core\Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

//		$dispatcher->connect(
//			'Refactory\Login\Controller\PasswordController', 'sendResetRequest',
//			'Refactory\Notifications\Service\SlotService', 'dispatchNotifications'
//		);
    }
}
