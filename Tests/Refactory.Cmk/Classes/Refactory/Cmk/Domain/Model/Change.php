<?php
namespace Refactory\Cmk\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Cmk".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * TODO: hidable model, validation
 * @Flow\Entity
 */
class Change {

	/**
	 * @var string
	 * Object
	 */
	protected $workspace;

	/**
	 * @var string
	 */
	protected $summary;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $motivation;

	/**
	 * @var string
	 */
	protected $feasibility;

//	/**
//	 * @var Object
//	 */
//	protected $documents;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Refactory\Cmk\Domain\Model\Change>
	 * @ORM\ManyToMany(inversedBy="relatedChanges")
	 * @ORM\OrderBy({"summary" = "ASC"})
	 * @Flow\Lazy
	 */
	protected $relatedChanges;

	/**
	 * @var \DateTime
	 */
	protected $created;

	/**
	 * @var \DateTime
	 */
	protected $updated;

	/**
	 * @var \DateTime
	 */
	protected $implementationDate;

	/**
	 *
	 * @var \DateTime
	 */
	protected $deadline;

//	/**
//	 * Planned release
//	 *
//	 * @var Object
//	 */
//	protected $plannedRelease;

	/**
	 * Archive Id
	 *
	 * @var string
	 */
	protected $archiveId;

	/**
	 * @var Object
	 */
	protected $createdBy;

	/**
	 * @var Object
	 */
	protected $responsiblePerson;

	/**
	 * @var Object
	 */
	protected $company;

	/**
	 * involvedCompanies
	 *
	 * @var Object
	 */
	protected $involvedCompanies;

	/**
	 * impacts
	 *
	 * @var Object
	 */
	protected $impacts;

	/**
	 * components
	 *
	 * @var Object
	 */
	protected $components;

	/**
	 * Status
	 *
	 * @var Object
	 */
	protected $status;

	/**
	 * Notes
	 * TODO: Make more notes
	 * @var string
	 */
	protected $notes;

}