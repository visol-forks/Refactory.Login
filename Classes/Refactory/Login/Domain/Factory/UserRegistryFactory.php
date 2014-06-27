<?php
namespace Refactory\Login\Domain\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Refactory\Login\Domain\Model\UserRegistry;
/**
 * A factory for conveniently creating new accounts
 *
 * @Flow\Scope("singleton")
 */
class UserRegistryFactory {

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * @param \TYPO3\Party\Domain\Model\Person $person
	 * @param $request
	 * @param string $passwordHashingStrategy
	 * @return \Refactory\Login\Domain\Model\UserRegistry
	 */
	public function createUserRegistryEntry($person, $request, $passwordHashingStrategy = 'default') {
		list($generatedToken, $salt) = explode(',', \TYPO3\Flow\Security\Cryptography\SaltedMd5HashingStrategy::generateSaltedMd5($person->getName()->getAlias()));

		$userEntry = new UserRegistry();
		$userEntry->setPerson($person);
		$userEntry->setDate(new \DateTime());
		$userEntry->setToken($generatedToken);
		$userEntry->setIp($request->getHttpRequest()->getClientIpAddress());

		$password = $request->getArgument('password');
		$userEntry->setCredentialsSource($this->hashService->hashPassword($password, $passwordHashingStrategy));

		return $userEntry;
	}
}
?>