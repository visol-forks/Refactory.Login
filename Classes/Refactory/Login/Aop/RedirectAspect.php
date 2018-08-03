<?php
namespace Refactory\Login\Aop;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Login".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Aspect
 */
class RedirectAspect
{

    /**
     * @Flow\Around("method(TYPO3\Flow\Mvc\Controller\AbstractController->redirect())")
     * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
     * @return void
     */
    public function redirectAspect(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint)
    {
        $proxy = $joinPoint->getProxy();

        $request = ObjectAccess::getProperty($proxy, 'request', true);
        if ($request->getFormat() === 'json') {
            $view = ObjectAccess::getProperty($proxy, 'view', true);
            $uriBuilder = ObjectAccess::getProperty($proxy, 'uriBuilder', true);

            $actionName = $joinPoint->getMethodArgument('actionName');
            $arguments = $joinPoint->getMethodArgument('arguments');
            $controllerName = $joinPoint->getMethodArgument('controllerName');
            $packageKey = $joinPoint->getMethodArgument('packageKey');

            if ($packageKey !== null && strpos($packageKey, '\\') !== false) {
                list($packageKey, $subpackageKey) = explode('\\', $packageKey, 2);
            } else {
                $subpackageKey = null;
            }

            $view->assign('value', ['redirect' => $uriBuilder->setCreateAbsoluteUri(true)->uriFor($actionName, $arguments, $controllerName, $packageKey, $subpackageKey)]);

            ObjectAccess::setProperty($proxy, 'view', $view, true);
        } else {
            $joinPoint->getAdviceChain()->proceed($joinPoint);
        }
    }
}
