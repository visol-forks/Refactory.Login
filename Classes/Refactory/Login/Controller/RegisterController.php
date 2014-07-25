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
	 * @var \TYPO3\Flow\I18n\Translator
	 * @Flow\Inject
	 */
	protected $translator;

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
		$response = array();

		if ($this->request->getArgument('password') === $this->request->getArgument('confirmPassword')) {
			$user = new Person();
			$name = new PersonName('', 'Undefined', '', 'Undefined', '', $identifier);
			$user->setName($name);

			$electronicAddress = new ElectronicAddress();
			$electronicAddress->setIdentifier($email);
			$electronicAddress->setType('Email');

			$user->addElectronicAddress($electronicAddress);

			$this->userRepository->add($user);

			// TODO Fix?! Workaround
			$this->persistenceManager->persistAll();

			$user->setPrimaryElectronicAddress($electronicAddress);

			$this->userRepository->update($user);

			try {
				$userRegistry = $this->userRegistryFactory->createUserRegistryEntry($user, $this->request);

				$this->userRegistryRepository->add($userRegistry);

				// TODO Send notification
				$uriBuilder = $this->controllerContext->getUriBuilder();
				$uri =  $uriBuilder->uriFor('verifyUser', array('user' => $user), NULL, NULL);
				$response = array('status' => 'OK', 'redirect' => $uri);
			} catch (Exception $exception) {
				$response['status'] = 'OK';
				$response['message']['type'] = 'error';
				$response['message']['label'] = 'The registration service catch an unexpected error!';
			}
		} else {
			$response['status'] = 'OK';
			$response['message']['type'] = 'error';
			$response['message']['label'] = 'The given passwords should be the same!';
		}

		$this->view->assign('value', $response);
	}

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
	 */
	public function verifyUserAction() {
		if ($this->request->hasArgument('user')) {
			$user = $this->request->getArgument('user');
			$this->persistenceManager->getObjectByIdentifier($user['__identity'], '\TYPO3\Party\Domain\Model\Person');
			$this->view->assign('user', $user);
		}
	}

	/**
	 * @param \TYPO3\Party\Domain\Model\Person $user
	 * @Flow\IgnoreValidation("$user")
	 * @param $authenticationToken
	 */
	public function verifiedUserAction($user, $authenticationToken) {
		// TODO
	}
}

?>