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

use foo\bar\Exception;
use Refactory\Login\Http\Response;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authentication\Token\PasswordToken;
use TYPO3\Party\Domain\Model\PersonName;
use TYPO3\Party\Domain\Model\Person;
use \TYPO3\Party\Domain\Model\ElectronicAddress;

/**
 * Password controller for the Refactory.Login package

 */
class RegisterController extends \TYPO3\Flow\Mvc\Controller\ActionController {

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
	 * @var \TYPO3\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @var \Refactory\Login\Domain\Repository\UserRepository
	 * @Flow\Inject
	 */
	protected $userRepository;

	/**
	 * @var \Refactory\Login\Domain\Repository\UserRegistryRepository
	 * @Flow\Inject
	 */
	protected $userRegistryRepository;

	/**
	 * @var \Refactory\Login\Domain\Factory\UserRegistryFactory
	 * @Flow\Inject
	 */
	protected $userRegistryFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Display reset a password request form
	 */
	public function newAction() {
	}

	/**
	 * @param string $identifier
	 * @Flow\Validate(argumentName="identifier", type="NotEmpty")
	 * @Flow\Validate(argumentName="identifier", type="StringLength", options={ "minimum"=1, "maximum"=255 })
	 * @Flow\Validate(argumentName="identifier", type="\Refactory\Login\Validation\Validator\AccountExistsValidator")
	 * @param string $email
	 * @Flow\Validate(argumentName="email", type="NotEmpty")
	 * @Flow\Validate(argumentName="email", type="\TYPO3\Flow\Validation\Validator\EmailAddressValidator")
	 * @return void
	 */
	public function createAction($identifier, $email) {
		if ($this->request->getArgument('password') === $this->request->getArgument('confirmPassword')) {
			$user = new Person();
			$name = new PersonName('', 'Undefined', '', 'Undefined', '', $identifier);
			$user->setName($name);

			$electronicAddress = new ElectronicAddress();
			$electronicAddress->setIdentifier($email);
			$electronicAddress->setType('Email');

			$user->addElectronicAddress($electronicAddress);
			$user->setPrimaryElectronicAddress($electronicAddress);
			$this->userRepository->add($user);

			try {
				$userRegistry = $this->userRegistryFactory->createUserRegistryEntry($user, $this->request);

				$this->userRegistryRepository->add($userRegistry);

				$this->emitVerifyAccount(array('to' => $user, 'authenticationToken' => $userRegistry->getToken(), 'controllerContext' => $this->controllerContext));

				$this->redirect('verifyUser', NULL, NULL, array('user' => $user));
			} catch (Exception $exception) {
				$response = new Response();
				$response->setType('error');
				$response->setMessage('The registration service catch an unexpected error!');
				$this->view->assign('value', $response);
			}
		} else {
			$response = new Response();
			$response->setType('error');
			$response->setMessage('The given passwords should be the same!');
			$this->view->assign('value', $response);
		}
	}

	/**
	 * Trigger Signal
	 *
	 * @param $arguments
	 * @return void
	 * @Flow\Signal
	 */
	public function emitVerifyAccount($arguments) {}

	/**
	 * Checks if username is already in use then returns a json response
	 *
	 * @param string $identifier
	 * @return void
	 */
	public function verifyAccountExistsAction($identifier) {
		$authenticationProviderName = isset($this->options['authenticationProviderName']) ? $this->options['authenticationProviderName'] : 'DefaultProvider';

		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($identifier, $authenticationProviderName);

		if ($account instanceof \TYPO3\Flow\Security\Account) {
			$isAvailable = FALSE;
		} else {
			$isAvailable = TRUE;
		}

		$this->view->assign('value', array('valid' => $isAvailable));
	}

	/**
	 * @return void
	 */
	public function verifyUserAction() {
		if ($this->request->hasArgument('user')) {
			$user = $this->request->getArgument('user');
			$this->persistenceManager->getObjectByIdentifier($user['__identity'], '\TYPO3\Party\Domain\Model\Person');
			$this->view->assign('user', $user);
		}
	}

	/**
	 * @return void
	 */
	public function verifiedUserAction() {
		if ($this->request->hasArgument('token')) {
			$isActiveToken = $this->userRegistryRepository->isActiveToken($this->request->getArgument('token'));
			$this->view->assign('isActiveToken', $isActiveToken);
		} else {
			$this->view->assign('hasUser', TRUE);
		}
	}
}