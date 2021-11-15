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

use Refactory\Login\Http\Response;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Error\Messages\Message;

/**
 * A controller which allows for logging into an application
 *
 * @Flow\Scope("singleton")
 */
class LoginController extends AbstractAuthenticationController
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
     * @var  \Neos\Flow\I18n\Service
     */
    protected $i18nService;

    /**
     * @var \Neos\Flow\I18n\Translator
     * @Flow\Inject
     */
    protected $translator;

    protected function initializeAction()
    {
        # Set locale based on Accept-Language
        $detector = new \Neos\Flow\I18n\Detector();
        $acceptLanguageHeader = $this->request->getHttpRequest()->getHeaderLine('Accept-Language');
        $language = $detector->detectLocaleFromHttpHeader($acceptLanguageHeader);
        $this->i18nService->getConfiguration()->setCurrentLocale($language);
    }

    /**
     * This action is used to show the login form.
     * If a user is already authenticated it will be redirected to the signedIn action or a custom redirect
     * @param string $username
     */
    public function loginAction($username = '')
    {
        if ($this->authenticationManager->isAuthenticated()) {
            if (isset($this->settings['authenticatedRedirect'])) {
                $redirect = $this->settings['authenticatedRedirect'];
                $this->redirect($redirect['actionName'], $redirect['controller'], $redirect['package']);
            }
            $this->redirect('signedIn');
        }

        $this->view->assign('username', $username);
    }

    /**
     * Authenticates an account by invoking the Provider based Authentication Manager.
     *
     * On successful authentication redirects to the list of posts, otherwise returns
     * to the login screen.
     *
     * @return void
     * @throws \Neos\Flow\Security\Exception\AuthenticationRequiredException
     */
    public function authenticateAction()
    {
        $authenticationException = null;
        try {
            $this->authenticationManager->authenticate();
        } catch (\Neos\Flow\Security\Exception\AuthenticationRequiredException $exception) {
            $authenticationException = $exception;

            $response = new Response();
            $response->setType('error');
            $response->setMessage('The entered username or password was wrong!');
            $this->view->assign('value', $response);
        }

        if ($this->authenticationManager->isAuthenticated()) {
            $storedRequest = $this->securityContext->getInterceptedRequest();
            if ($storedRequest !== null) {
                $this->securityContext->setInterceptedRequest(null);
            }
            $this->onAuthenticationSuccess($storedRequest);
        } else {
            $this->onAuthenticationFailure($authenticationException);
        }
    }

    /**
     * Logs out a - possibly - currently logged in account.
     *
     * @return void
     */
    public function logoutAction()
    {
        parent::logoutAction();

        switch ($this->request->getFormat()) {
            default:
                $this->addFlashMessage($this->translator->translateById('loginController.logout.successful.message', [], null, null, $this->settings['translationSourceName'], $this->settings['translationPackageKey']), $this->translator->translateById('loginController.logout.successful.title', [], null, null, $this->settings['translationSourceName'], $this->settings['translationPackageKey']), Message::SEVERITY_WARNING);
                $this->redirect('login');
                break;
        }
    }


    /**
     * Signed in dummy action to show you that your really signed in
     *
     * @return void
     */
    public function signedInAction()
    {
    }

    /**
     * Is called if authentication was successful.
     *
     * @param \Neos\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
     * @return string
     */
    public function onAuthenticationSuccess(\Neos\Flow\Mvc\ActionRequest $originalRequest = null)
    {
        if ($originalRequest !== null) {
            $this->redirectToRequest($originalRequest);
        } else {
            if (isset($this->settings['authenticatedRedirect'])) {
                $packageKey     = $this->settings['authenticatedRedirect']['package'];
                $controllerName = $this->settings['authenticatedRedirect']['controller'];
                $actionName     = $this->settings['authenticatedRedirect']['actionName'];
                $this->redirect($actionName, $controllerName, $packageKey);
            } else {
                $this->redirect('signedIn');
            }
        }
    }

    /**
     * Is called if authentication failed.
     *
     * Override this method in your login controller to take any
     * custom action for this event. Most likely you would want
     * to redirect to some action showing the login form again.
     *
     * @param \Neos\Flow\Security\Exception\AuthenticationRequiredException $exception The exception thrown while the authentication process
     * @return void
     */
    protected function onAuthenticationFailure(\Neos\Flow\Security\Exception\AuthenticationRequiredException $exception = null)
    {
    }
}
