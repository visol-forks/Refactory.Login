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
use TYPO3\Flow\Security\Authentication\Token\PasswordToken;
use TYPO3\Party\Domain\Model\AbstractParty;

/**
 * Password controller for the Refactory.Login package

 */
class PasswordController extends \TYPO3\Flow\Mvc\Controller\ActionController {

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
	 * @Flow\Inject
	 * @var \Refactory\Login\Service\AccountManagementService
	 */
	protected $accountManagementService;

	/**
	 * @var \TYPO3\Flow\Security\AccountRepository
	 * @Flow\Inject
	 */
	protected $accountRepository;

	/**
	 * @var \Refactory\Login\Domain\Repository\UserRepository
	 * @Flow\Inject
	 */
	protected $userRepository;

	/**
	 * Display reset a password request form
	 */
	public function resetRequestAction() {
	}

	/**
	 * @param $arguments
	 * @return void
	 * @Flow\Signal
	 */
	protected function emitSendResetRequest($arguments) {}

	/**
	 * @param string $identifier
	 */
	public function sendResetRequestAction($identifier) {
		$response = array();
		$person = NULL;
		$resetPasswordToken = NULL;

		if (empty($identifier)) {
			$response['status'] = 'OK';
			$response['message']['type'] = 'error';
			// TODO: Translate
			$response['message']['label'] = 'No username or e-mail address was given!';
		} else {
			$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($identifier, 'DefaultProvider');
			if ($account !== NULL) {
				$person = $account->getParty();
				$resetPasswordToken = $this->accountManagementService->generateResetPasswordTokenForParty($person, $this->request);
			} else {
				$person = $this->userRepository->findByPrimaryElectronicAddress($identifier)->getFirst();
				if (is_subclass_of($person, '\TYPO3\Party\Domain\Model\AbstractParty')) {
					$resetPasswordToken = $this->accountManagementService->generateResetPasswordTokenForParty($person, $this->request);
				}
			}

			$this->emitSendResetRequest(array('controllerContext' => $this->controllerContext, 'resetPasswordToken' => $resetPasswordToken->getToken(), 'to' => $person));

			$uriBuilder = $this->controllerContext->getUriBuilder();
			$uri =  $uriBuilder->uriFor('reset', array('identifier' => $identifier), NULL, NULL);
			$response['status'] = 'OK';
			$response['redirect'] = $uri;
		}

		$this->view->assign('value', $response);
	}

	/**
	 * Display set new password form, after reset request
	 */
	public function resetAction() {
		if ($this->request->hasArgument('token')) {
			$response = array();
			$isTokenActive = $this->accountManagementService->isTokenActive($this->request->getArgument('token'));
			$isPasswordChanged = FALSE;
			$this->view->assign('isTokenActive', $isTokenActive);
			$this->view->assign('token', $this->request->getArgument('token'));
			if ($this->request->hasArgument('newPassword') && $this->request->hasArgument('newPasswordRepeat') && $isTokenActive) {

				if ($this->request->getArgument('newPassword') === $this->request->getArgument('newPasswordRepeat')) {
					$account = $this->accountManagementService->getAccountByToken($this->request->getArgument('token'));
					$isPasswordChanged = $this->accountManagementService->resetPassword($account, $this->request->getArgument('newPassword'));

					if ($isPasswordChanged) {
						$this->accountManagementService->deactivateToken($this->request->getArgument('token'));
						$uriBuilder = $this->controllerContext->getUriBuilder();
						$uri =  $uriBuilder->uriFor('complete',array('user' => $account->getParty()), NULL, NULL);

						$response['status'] = 'OK';
						$response['redirect'] = $uri;
					} else {
						$response['status'] = 'OK';
						$response['message']['type'] = 'error';
						$response['message']['label'] = 'Wijzigen wachtwoord is mislukt';
					}
				}
				else {
					$response['status'] = 'OK';
					$response['message']['type'] = 'error';
					$response['message']['label'] = 'De wachtwoorden zijn niet gelijk';
				}

				$this->view->assign('value', $response);
			}
			$this->view->assign('isPasswordChanged', $isPasswordChanged);
		} elseif ($this->request->hasArgument('identifier')) {
			$this->view->assign('hasUser', TRUE);
		}
	}

	/**
	 *
	 */
	public function completeAction() {}
}

?>