<?php
namespace Refactory\Login\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flow.Login".            *
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
class ResetPasswordToken {

	/**
	 * @ORM\ManyToOne
	 * @var \TYPO3\Flow\Security\Account
	 */
	protected $account;

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
	 * @var boolean
	 */
	protected $active;

	/**
	 * Set account
	 *
	 * @param \TYPO3\Flow\Security\Account $account
	 */
	public function setAccount($account) {
		$this->account = $account;
	}

	/**
	 * Get account
	 *
	 * @return \TYPO3\Flow\Security\Account
	 */
	public function getAccount() {
		return $this->account;
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
	 * Set active
	 *
	 * @param boolean $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * Get active
	 *
	 * @return boolean
	 */
	public function getActive() {
		return $this->active;
	}

	/**
	 * @param $expirationDate
	 * @return bool
	 */
	public function isActive($expirationDate) {
		$isActive = FALSE;
		if ($this->getActive()) {
			$tokenCreateDate = $this->getDate();
			$now = new \DateTime();
			if ($now->getTimestamp() - $tokenCreateDate->getTimestamp() <= $expirationDate) {
				$isActive = TRUE;
			}
		}
		return $isActive;
	}
}
?>