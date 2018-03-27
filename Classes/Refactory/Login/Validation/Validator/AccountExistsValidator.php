<?php
namespace Refactory\Login\Validation\Validator;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Login".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * Validator for accounts
 */
class AccountExistsValidator extends \Neos\Flow\Validation\Validator\AbstractValidator {

	/**
	 * @Flow\Inject
	 * @var \Neos\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @param mixed $value The value that should be validated
	 * @return void
	 * @throws \Neos\Flow\Validation\Exception\InvalidSubjectException
	 */
	protected function isValid($value) {
		if (!is_string($value)) {
			throw new \Neos\Flow\Validation\Exception\InvalidSubjectException('The given value was not a string.', 1325155784);
		}

		$authenticationProviderName = isset($this->options['authenticationProviderName']) ? $this->options['authenticationProviderName'] : 'DefaultProvider';

		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($value, $authenticationProviderName);

		if ($account !== NULL) {
			$this->addError('The username is already in use.', 1325156008);
		}
	}

}