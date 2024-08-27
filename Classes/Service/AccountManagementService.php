<?php
namespace Refactory\Login\Service;

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
use Neos\Flow\Http\ServerRequestAttributes;
use Neos\Flow\Security\Account;
use Neos\Party\Domain\Model\AbstractParty;

/**
 * An AccountManagementService service
 *
 */
class AccountManagementService
{

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\AccountRepository
     */
    protected $accountRepository;

    /**
     * @Flow\Inject
     * @var \Refactory\Login\Domain\Repository\ResetPasswordTokenRepository
     */
    protected $resetPasswordTokenRepository;

    /**
     * @var \Neos\Flow\Security\Cryptography\HashService
     * @Flow\Inject
     */
    protected $hashService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Set a new password for the given account
     *
     * This allows for setting a new password for an existing user account.
     *
     * @param Account $account
     * @param $password
     * @param string $passwordHashingStrategy
     *
     * @return boolean
     */
    public function resetPassword(Account $account, $password, $passwordHashingStrategy = 'default')
    {
        $account->setCredentialsSource($this->hashService->hashPassword($password, $passwordHashingStrategy));
        $this->accountRepository->update($account);
        return true;
    }

    /**
     * @param Account $account
     * @param \Neos\Flow\Mvc\ActionRequest $request
     * @return \Refactory\Login\Domain\Model\ResetPasswordToken
     */
    public function generateResetPasswordToken(Account $account, \Neos\Flow\Mvc\ActionRequest $request = null)
    {
        list($generatedToken, $salt) = explode(',', \Neos\Flow\Security\Cryptography\SaltedMd5HashingStrategy::generateSaltedMd5($account->getAccountIdentifier()));
        $resetPasswordToken = new \Refactory\Login\Domain\Model\ResetPasswordToken();
        $resetPasswordToken->setDate(new \DateTime());
        $resetPasswordToken->setAccount($account);
        $resetPasswordToken->setToken($generatedToken);
        // There is no more getClientIpAddress() method in ActionRequest
        // $resetPasswordToken->setIp($request->getHttpRequest()->getClientIpAddress());
        $resetPasswordToken->setIp($request->getHttpRequest()->getAttribute(ServerRequestAttributes::CLIENT_IP));
        $resetPasswordToken->setActive(true);
        $this->resetPasswordTokenRepository->add($resetPasswordToken);
        return $resetPasswordToken;
    }

    /**
     * @param AbstractParty $party
     * @param \Neos\Flow\Mvc\ActionRequest $request
     * @return \Refactory\Login\Domain\Model\ResetPasswordToken
     * @throws \Exception
     * @throws \Neos\Flow\Exception
     */
    public function generateResetPasswordTokenForParty(AbstractParty $party, \Neos\Flow\Mvc\ActionRequest $request = null)
    {
        $account = $this->getAccountByParty($party);
        // There is no more getClientIpAddress() method in ActionRequest
        // $request->getHttpRequest()->getClientIpAddress();
        $request->getHttpRequest()->getAttribute(ServerRequestAttributes::CLIENT_IP);
        return $this->generateResetPasswordToken($account, $request);
    }

    /**
     * @param $token
     * @return bool
     */
    public function isTokenActive($token)
    {
        $isTokenActive = false;
        $resetPasswordToken = $this->resetPasswordTokenRepository->findOneByToken($token);
        if ($resetPasswordToken) {
            $isTokenActive = $resetPasswordToken->isActive($this->settings['tokenExpiration']);
        }
        return $isTokenActive;
    }

    /**
     * @param string $token
     */
    public function deactivateToken($token)
    {
        $resetPasswordToken = $this->resetPasswordTokenRepository->findOneByToken($token);
        if ($resetPasswordToken) {
            $resetPasswordToken->setActive(false);
            $this->resetPasswordTokenRepository->update($resetPasswordToken);
        }
    }

    /**
     * Method to find account by given party
     * @param AbstractParty $party
     * @return \Neos\Flow\Security\Account
     */
    public function getAccountByParty(AbstractParty $party)
    {
        return $party->getAccounts()->first();
    }

    /**
     * Method to find account by given token
     * @param string $token
     * @return \Neos\Flow\Security\Account
     */
    public function getAccountByToken($token)
    {
        return $this->resetPasswordTokenRepository->findOneByToken($token)->getAccount();
    }
}
