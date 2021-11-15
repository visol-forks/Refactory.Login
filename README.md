Refactory.Login
===============

This package is a lightweight authentication wrapper around a given package.
A Flow Framework package with the following features:

 - Login and logout an account
 - Reset a password for a given account.

These features have their own workflows, based on some research the idea was to make the registration as basic as possible.
The package has been built on top of the features that are provided in the security framework of the Flow Framework and require only a little configuration.

Usage:
- Security layer for any application
- Inspiration

## Quickstart

This fork currently is not published on packagist. You must require it in the repositories section of your `composer.json`:

    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/visol/Refactory.Login.git"
        }
    ],

Then you can require it in the `require` section of your `composer.json`:

    "require": {
        "refactory/login": "^3.0",
    },

Then run `composer update` to actually install the package.

Then you need to run migrations to include the tables.

	./flow doctrine:migrate

To enable routing to package

	-
	  name: 'Login'
	  uriPattern: '<LoginSubroutes>'
	  subRoutes:
	    LoginSubroutes:
	      package: Refactory.Login

## Overview

### Login screen

![Login Screen](Documentation/Images/LoginScreen.png)

### Reset password

![Reset Password Screen](Documentation/Images/ResetPassword.png)


## Configuration

### Authentication setup

The initial view will show a login panel.

When authenticated but not configured, the package will redirect to the Signed In page by default.
The Signed In view will show you with what "account.identifier" you have been authenticated.

Through *Configuration/Settings.yaml* you will be able to configure options like redirects to a package, registration and so on.

### Routing

To be able to address the login feature you will need to add these routes in the general Configuration/Routes.yaml

	-
	  name: 'Login'
	  uriPattern: '<LoginSubroutes>'
	  subRoutes:
	    LoginSubroutes:
	      package: Refactory.Login

### Account ViewHelper

Add the ViewHelper to Fluid and call the ViewHelper function.

	{namespace secure=Refactory\Login\ViewHelpers}

	<secure:account propertyPath="name" />

### Redirect to Login page

If the action is unauthorized, the Flow Framework will redirect the package to a location set with the Settings.yaml configuration.

	Neos:
	  Flow:
	    security:
	      authentication:
	        providers:
	          DefaultProvider:
	            entryPoint: 'WebRedirect'
	            entryPointOptions:
	              routeValues:
	                '@package': 'Refactory.Login'
	                '@controller': 'Login'
	                '@action': 'login'

See for reference: https://flowframework.readthedocs.io/en/stable/TheDefinitiveGuide/PartIII/Security.html

## Compatibility and Maintenance

This package is currently being maintained for the following versions:

| Neos Version        | Version | Maintained |
|----------------------------|----------------------------------|------------|
| Flow 3.x         | 1.x  | No |
| Flow 4.x/5.x         | 2.x | Yes |
| Flow 6.x and above | 3.x  | Yes    |

## Credits

Author: Sebastiaan van Parijs (<svparijs@refactory.it>) 

Maintainer of this fork: visol digitale Dienstleistungen GmbH, www.visol.ch
