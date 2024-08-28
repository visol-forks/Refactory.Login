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
use Neos\Flow\Exception;
use Neos\Flow\Http\ServerRequestAttributes;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Cryptography\HashService;
use Neos\Party\Domain\Model\AbstractParty;
use Refactory\Login\Domain\Model\ResetPasswordToken;
use Refactory\Login\Domain\Repository\ResetPasswordTokenRepository;

/**
 * An AccountManagementService service
 *
 */
class AccountManagementService
{

    /**
     * @Flow\Inject
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @Flow\Inject
     * @var ResetPasswordTokenRepository
     */
    protected $resetPasswordTokenRepository;

    /**
     * @var HashService
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
     * @param ActionRequest $request
     * @return ResetPasswordToken
     */
    public function generateResetPasswordToken(Account $account, ActionRequest $request = null)
    {
        $salt = bin2hex(random_bytes(22));
        $hashedIdentifier = hash('sha256', $account->getAccountIdentifier() . $salt);

        $resetPasswordToken = new ResetPasswordToken();
        $resetPasswordToken->setDate(new \DateTime());
        $resetPasswordToken->setAccount($account);
        $resetPasswordToken->setToken($hashedIdentifier);
        $resetPasswordToken->setIp($request->getHttpRequest()->getAttribute(ServerRequestAttributes::CLIENT_IP));
        $resetPasswordToken->setActive(true);
        $this->resetPasswordTokenRepository->add($resetPasswordToken);
        return $resetPasswordToken;
    }

    /**
     * @param AbstractParty $party
     * @param ActionRequest $request
     * @return ResetPasswordToken
     * @throws \Exception
     * @throws Exception
     */
    public function generateResetPasswordTokenForParty(AbstractParty $party, ActionRequest $request = null)
    {
        $account = $this->getAccountByParty($party);
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
     * @return Account
     */
    public function getAccountByParty(AbstractParty $party)
    {
        return $party->getAccounts()->first();
    }

    /**
     * Method to find account by given token
     * @param string $token
     * @return Account
     */
    public function getAccountByToken($token)
    {
        return $this->resetPasswordTokenRepository->findOneByToken($token)->getAccount();
    }
}
