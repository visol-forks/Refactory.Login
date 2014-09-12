<?php
namespace Refactory\Login\Domain\Model;

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
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class UserRegistry {

	/**
	 * @ORM\ManyToOne
	 * @var \TYPO3\Party\Domain\Model\Person
	 */
	protected $person;

	/**
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var string
	 */
	protected $credentialsSource;

	/**
	 * @var bool
	 */
	protected $accountVerified = FALSE;

	/**
	 * Set person
	 *
	 * @param \TYPO3\Party\Domain\Model\Person $person
	 */
	public function setPerson($person) {
		$this->person = $person;
	}

	/**
	 * Get person
	 *
	 * @return \TYPO3\Party\Domain\Model\Person
	 */
	public function getPerson() {
		return $this->person;
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Set ip
	 *
	 * @param string $ip
	 */
	public function setIp($ip) {
		$this->ip = $ip;
	}

	/**
	 * Get ip
	 *
	 * @return string
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * Set token
	 *
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}

	/**
	 * Get token
	 *
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Returns the credentials source
	 *
	 * @return mixed The credentials source
	 */
	public function getCredentialsSource() {
		return $this->credentialsSource;
	}

	/**
	 * Sets the credentials source
	 *
	 * @param mixed $credentialsSource The credentials source
	 * @return void
	 */
	public function setCredentialsSource($credentialsSource) {
		$this->credentialsSource = $credentialsSource;
	}
}