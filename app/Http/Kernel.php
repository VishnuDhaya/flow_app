<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [

        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
             \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:240,1',
            'bindings',
           
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.partner' => \App\Http\Middleware\PartnerAuthMiddleware::class,
        'auth.app' => \App\Http\Middleware\AppUserAuthMiddleware::class,
        'auth.cust' => \App\Http\Middleware\CustAppUserAuthMiddleware::class,
        'auth.internal' => \App\Http\Middleware\InternalAPIUserAuthMiddleware::class,
        'auth.inv' => \App\Http\Middleware\InvUserAuthMiddleware::class,
        'auth.core' => \App\Http\Middleware\CoreUserAuthMiddleware::class,
        'auth.mobile' => \App\Http\Middleware\MobileAppAuthMiddleware::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.wallet_app' => \App\Http\Middleware\FlowWalletAppAuthMiddleware::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
	    'cors' => \Fruitcake\Cors\HandleCors::class,
        'flow' => \App\Http\Middleware\FlowMiddleware::class,
        'flow_cust' => \App\Http\Middleware\FlowCustAppMiddleware::class,
        'ait' => \App\Http\Middleware\AfricasSMSMiddleware::class,
        'aitv' => \App\Http\Middleware\AfricasVoiceMiddleware::class,
        'leadportal' => \App\Http\Middleware\LeadportalUserMiddleware::class,
        'transformer' => \App\Http\Middleware\RequestTransformer::class,
        'validate.acc_number' => \App\Http\Middleware\AccNumberValidationMiddleware::class,
        'encrypt.partner' => \App\Http\Middleware\PartnerEncryptMiddleware::class,
        'activity' => \App\Http\Middleware\RMActivityLogMiddleware::class
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\FlowMiddleware::class,  
        \App\Http\Middleware\AppUserAuthenticate::class,
        \App\Http\Middleware\CoreUserAuthenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
