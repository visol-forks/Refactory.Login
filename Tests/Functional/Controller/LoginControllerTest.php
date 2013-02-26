<?php
namespace Flow\Login\Tests\Functional\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flow.Login".  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

use TYPO3\Flow\Mvc\Controller\ActionController;

class LoginControllerTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * Note: this will implicitly enable testable HTTP as well.
	 *
	 * @var boolean
	 */
	protected $testableSecurityEnabled = TRUE;

	/**
	 * @test
	 */
	public function routeReachesLoginControllerIndexAction() {
		$response = $this->browser->request('http://localhost/login');
		$this->assertEquals(200, $response->getStatusCode());
	}

	public function permissionDeniedOnSignedInActionForEveryOne() {
		$response = $this->browser->request('http://localhost/login/signedin.html');
		$this->assertEquals(403, $response->getStatusCode());
	}

	public function permissionGrantedOnSignedInActionForAuthenticatedUser() {
		$this->authenticateRoles(array('Administrator'));
		$response = $this->browser->request('http://localhost/login/signedin.html');
		$this->assertEquals(200, $response->getStatusCode());
	}
}

?>