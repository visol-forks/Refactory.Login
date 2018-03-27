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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * Repository for parties
 *
 * @Flow\Scope("singleton")
 */
class UserRepository extends Repository {

	const ENTITY_CLASSNAME = '\TYPO3\Party\Domain\Model\Person';

	public function findByPrimaryElectronicAddress($electronicAddress) {
		$query = $this->createQuery();
		return $query->matching(
			$query->equals('primaryElectronicAddress.identifier', $electronicAddress)
		)->execute();
	}

}