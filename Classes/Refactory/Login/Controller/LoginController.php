<?php
namespace Refactory\Login\Controller;

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
use TYPO3\Flow\Error\Message;

/**
 * A controller which allows for loggin into a application
 *
 * @Flow\Scope("singleton")
 */
class LoginController extends \TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController {

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('text/html', 'application/json');

	/**
	 * @var array
	 */
	protected $viewFormatToObjectNameMap = array(
		'html'  => 'TYPO3\Fluid\View\TemplateView',
		'json'  => 'TYPO3\Flow\Mvc\View\JsonView');

	/**
	 * @var \TYPO3\Flow\I18n\Translator
	 * @Flow\Inject
	 */
	protected $translator;

	/**
	 * This action is used to show the login form.
	 * If a user is already authenticated it will be redirected to the signedIn action or a custom redirect
	 */
	public function loginAction() {
		if ($this->authenticationManager->isAuthenticated()) {
			if(isset($this->settings['authenticatedRedirect'])) {
				$redirect = $this->settings['authenticatedRedirect'];
				$this->redirect($redirect['actionName'], $redirect['controller'], $redirect['packageKey']);
			}
			$this->redirect('signedIn');
		}
	}

	/**
	 * Authenticates an account by invoking the Provider based Authentication Manager.
	 *
	 * On successful authentication redirects to the list of posts, otherwise returns
	 * to the login screen.
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Security\Exception\AuthenticationRequiredException
	 */
	public function authenticateAction() {
		$authenticationException = NULL;
		try {
			$this->authenticationManager->authenticate();
		} catch (\TYPO3\Flow\Security\Exception\AuthenticationRequiredException $exception) {
			$authenticationException = $exception;

			$response = array();
			$response['status'] = 'OK';
			$response['message']['type'] = 'error';
			$response['message']['label'] = 'The entered username or password was wrong!';

			$this->view->assign('value', $response);
		}

		if ($this->authenticationManager->isAuthenticated()) {
			$storedRequest = $this->securityContext->getInterceptedRequest();
			if ($storedRequest !== NULL) {
				$this->securityContext->setInterceptedRequest(NULL);
			}
			$this->onAuthenticationSuccess($storedRequest);
		} else {
			$this->onAuthenticationFailure($authenticationException);
		}
	}

	/**
	 * Logs out a - possibly - currently logged in account.
	 *
	 * @return void
	 */
	public function logoutAction() {
		parent::logoutAction();

		switch ($this->request->getFormat()) {
			default:
				$this->addFlashMessage('Successfully signed out.', 'Logged Out', Message::SEVERITY_WARNING);
				$this->redirect('login');
				break;
		}
	}


	/**
	 * Signed in dummy action to show you that your really signed in
	 *
	 * @return void
	 */
	public function signedInAction(){
	}

	/**
	 * Is called if authentication was successful.
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
	 * @return string
	 */
	public function onAuthenticationSuccess(\TYPO3\Flow\Mvc\ActionRequest $originalRequest = NULL) {
		$uriBuilder = $this->controllerContext->getUriBuilder();
		if ($originalRequest !== NULL) {
			$uri =  $uriBuilder->uriFor($originalRequest->getControllerActionName(), $originalRequest->getArguments(), $originalRequest->getControllerName(), $originalRequest->getControllerPackageKey());
		} else {
			if(isset($this->settings['authenticatedRedirect'])) {
				$packageKey     = $this->settings['authenticatedRedirect']['packageKey'];
				$controllerName = $this->settings['authenticatedRedirect']['controller'];
				$actionName     = $this->settings['authenticatedRedirect']['actionName'];
				$uri = $uriBuilder->uriFor($actionName, NULL, $controllerName, $packageKey);
			} else {
				$uri = $uriBuilder->uriFor('signedIn', NULL, 'Login', 'Refactory.Login');
			}
		}

		$response = array();
		$response['status'] = 'OK';
		$response['redirect'] = $uri;

		$this->view->assign('value', $response);
	}
}

?>
