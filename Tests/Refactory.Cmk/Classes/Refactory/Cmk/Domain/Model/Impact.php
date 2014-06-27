<?php
namespace Refactory\Cmk\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Cmk".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * TODO: Hidable, Validation
 * @Flow\Entity
 */
class Impact {

//	/**
//	 * Change
//	 *
//	 * @var Object
//	 */
//	protected $change;

	/**
	 * @var Object
	 */
	protected $relatedComponents;

	/**
	 * @var string
	 */
	protected $functionalImpact;

	/**
	 * @var string
	 */
	protected $technicalImpact;

	/**
	 * @var string
	 */
	protected $financialImpact;

	/**
	 * @var string
	 */
	protected $relatedProjects;

	/**
	 * @var Object
	 */
	protected $conclusion;

	/**
	 * conditions
	 *
	 * @var string
	 */
	protected $conditions;

	/**
	 * denial
	 *
	 * @var string
	 */
	protected $denial;

	/**
	 * additionalInformation
	 *
	 * @var string
	 */
	protected $additionalInformation;

	/**
	 * remark
	 *
	 * @var Object
	 */
	protected $remarks;

	/**
	 * member
	 *
	 * @var Object
	 */
	protected $createdBy;

	/**
	 * comapny
	 *
	 * @var Object
	 */
	protected $company;

	/**
	 * review
	 *
	 * @var string
	 */
	protected $review;
}