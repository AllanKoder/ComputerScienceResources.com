<?php

use JoelButcher\Socialstream\Features;
use JoelButcher\Socialstream\Providers;

return [
    'guard' => 'web', // used if Fortify is not installed
    'prompt' => 'Or Login Via',
    'providers' => [
        Providers::github(),
    ],
    'features' => [
        // Features::generateMissingEmails(),
        // Features::globalLogin(),
        // Features::authExistingUnlinkedUsers(),
        Features::createAccountOnFirstLogin(),
        Features::rememberSession(),
        Features::providerAvatars(),
        Features::refreshOAuthTokens(),
    ],
    'home' => '/',
    'redirects' => [
        'login' => '/',
        'register' => '/',
        'login-failed' => '/login',
        'registration-failed' => '/register',
        'provider-linked' => '/user/profile',
        'provider-link-failed' => '/user/profile',
    ],
];
