<?php
namespace Refactory\Login\Http;
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
 * Represents a Custom Json HTTP Response
 * @Flow\Scope("singleton")
 */
class Response {

    /**
     * @Flow\InjectConfiguration(package="Refactory.Login")
     * @var array
     */
    protected $settings;

	const TYPE_SUCCESS = 'Success';
	const TYPE_INFO = 'Info';
	const TYPE_WARNING = 'Warning';
	const TYPE_ERROR = 'Error';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var  string
	 */
	protected $message;

	/**
	 * @var \Neos\Flow\I18n\Translator
	 * @Flow\Inject
	 */
	protected $translator;

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->translator->translateByOriginalLabel($this->message, [], NULL, NULL, $this->settings['translationSourceName'], $this->settings['translationPackageKey']);
	}
}
