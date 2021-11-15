<?php
namespace Refactory\Login\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Refactory.Login".       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Refactory\Login\Domain\Model\ResetPasswordToken;
use Refactory\Login\Http\Response;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authentication\Token\PasswordToken;
use Neos\Party\Domain\Model\AbstractParty;

/**
 * Password controller for the Refactory.Login package

 */
class PasswordController extends \Neos\Flow\Mvc\Controller\ActionController
{

    /**
     * @var array
     */
    protected $supportedMediaTypes = ['text/html', 'application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
        'html'  => 'Neos\FluidAdaptor\View\TemplateView',
        'json'  => 'Neos\Flow\Mvc\View\JsonView'
    ];

    /**
     * @Flow\Inject
     * @var \Refactory\Login\Service\AccountManagementService
     */
    protected $accountManagementService;

    /**
     * @var \Neos\Flow\Security\AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @var \Refactory\Login\Domain\Repository\UserRepository
     * @Flow\Inject
     */
    protected $userRepository;

    /**
     * @var string
     * @Flow\InjectConfiguration(path="partyRepositoryClassName", package="Refactory.Login")
     */
    protected $partyRepositoryClassName;

    /**
     * Display reset a password request form
     */
    public function resetRequestAction()
    {
    }

    /**
     * @param $arguments
     * @return void
     * @Flow\Signal
     */
    protected function emitSendResetRequest($arguments)
    {
    }

    /**
     * @param string $identifier
     */
    public function sendResetRequestAction($identifier)
    {
        $person = null;
        $resetPasswordToken = null;

        if (empty($identifier)) {
            $response = new Response();
            $response->setType('error');
            $response->setMessage('No username or e-mail address was given!');
            $this->view->assign('value', $response);
        } else {
            $account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($identifier, 'DefaultProvider');
            if ($account !== null) {
                $partyRepository = $this->objectManager->get($this->partyRepositoryClassName);
                $person = $partyRepository->findOneHavingAccount($account);
                $resetPasswordToken = $this->accountManagementService->generateResetPasswordTokenForParty($person, $this->request);
            } else {
                $person = $this->userRepository->findByPrimaryElectronicAddress($identifier)->getFirst();
                if (is_subclass_of($person, AbstractParty::class)) {
                    $resetPasswordToken = $this->accountManagementService->generateResetPasswordTokenForParty($person, $this->request);
                }
            }

            if ($resetPasswordToken instanceof ResetPasswordToken) {
                $this->emitSendResetRequest(
                    [
                        'controllerContext' => $this->controllerContext,
                        'resetPasswordToken' => $resetPasswordToken->getToken(),
                        'recipient' => $person
                    ]
                );
            }

            $this->request->setFormat('json');
            $this->redirect('reset', null, null, ['identifier' => $identifier]);
        }
    }

    /**
     * Display set new password form, after reset request
     */
    public function resetAction()
    {
        if ($this->request->hasArgument('token')) {
            $isTokenActive = $this->accountManagementService->isTokenActive($this->request->getArgument('token'));
            $isPasswordChanged = false;
            $this->view->assign('isTokenActive', $isTokenActive);
            $this->view->assign('token', $this->request->getArgument('token'));
            if ($this->request->hasArgument('newPassword') && $this->request->hasArgument('newPasswordRepeat') && $isTokenActive) {

                if ($this->request->getArgument('newPassword') === $this->request->getArgument('newPasswordRepeat')) {
                    $account = $this->accountManagementService->getAccountByToken($this->request->getArgument('token'));
                    $isPasswordChanged = $this->accountManagementService->resetPassword($account, $this->request->getArgument('newPassword'));

                    if ($isPasswordChanged) {
                        $this->accountManagementService->deactivateToken($this->request->getArgument('token'));
                        $this->redirect('login', 'Login', null, ['username' => $account->getAccountIdentifier()]);
                    } else {
                        $response = new Response();
                        $response->setType('error');
                        $response->setMessage('Attempt to change password failed!');
                        $this->view->assign('value', $response);
                    }
                } else {
                    $response = new Response();
                    $response->setType('error');
                    $response->setMessage('Password and password confirmation are not identical!');

                    $this->view->assign('value', $response);
                }

            }
            $this->view->assign('isPasswordChanged', $isPasswordChanged);
        } elseif ($this->request->hasArgument('identifier')) {
            $this->view->assign('hasUser', true);
        }
    }
}
