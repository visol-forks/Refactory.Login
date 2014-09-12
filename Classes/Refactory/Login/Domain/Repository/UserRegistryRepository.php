<?php
namespace Refactory\Login\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Party".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Refactory\Login\Domain\Model\UserRegistry;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * Repository of registered users
 *
 * @Flow\Scope("singleton")
 */
class UserRegistryRepository extends Repository {

	/**
	 * @param $token
	 * @return boolean
	 */
	public function isActiveToken($token) {
		$query = $this->createQuery();

		$userRegistry = $query->matching(
			$query->equals('token', $token)
		)->execute()->getFirst();

		if ($userRegistry instanceof UserRegistry) {
			return TRUE;
		}
		return FALSE;
	}
}