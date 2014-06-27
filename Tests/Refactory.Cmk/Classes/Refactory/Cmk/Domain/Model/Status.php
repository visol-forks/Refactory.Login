<?php
namespace Refactory\Cmk\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Cmk".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Status {

	/**
	 * Label
	 * @var string
	 */
	protected $label;

	/**
	 * Description
	 * @var string
	 */
	protected $description;

}