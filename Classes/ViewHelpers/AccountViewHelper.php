<?php
namespace Refactory\Login\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flow.Login".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Neos\Flow\Annotations as Flow;

/**
 * Shows the name of the currently active user
 */
class AccountViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var \Neos\Flow\Security\Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var string
     * @Flow\InjectConfiguration(path="partyRepositoryClassName", package="Refactory.Login")
     */
    protected $partyRepositoryClassName;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\ObjectManagement\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @return string
     */
    public function render()
    {

        $partyRepository = $this->objectManager->get($this->partyRepositoryClassName);

        $tokens = $this->securityContext->getAuthenticationTokens();

        foreach ($tokens as $token) {
            if ($token->isAuthenticated()) {
                $person = $partyRepository->findOneHavingAccount($token->getAccount());
                return \Neos\Utility\ObjectAccess::getPropertyPath($person, $this->arguments['propertyPath']);
            }
        }

        return '';
    }

    /**
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('propertyPath', 'string', 'propertyPath', true, 'name');
    }
}