<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */
    'name' => 'TerraFusion',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    */
    'env' => getenv('APP_ENV') ?: 'production',

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    */
    'debug' => (bool) (getenv('APP_DEBUG') ?: true),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    */
    'url' => getenv('APP_URL') ?: 'http://localhost/TerraFusion',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    */
    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    */
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    */
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    */
    'key' => getenv('APP_KEY') ?: 'base64:'.base64_encode(random_bytes(32)),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    */
    'log' => [
        'default' => 'single',
        'path' => __DIR__.'/../../logs/app.log',
        'level' => 'debug',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    */
    'auth' => [
        'defaults' => [
            'guard' => 'web',
            'passwords' => 'users',
        ],
        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'api' => [
                'driver' => 'token',
                'provider' => 'users',
                'hash' => false,
            ],
        ],
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\Models\User::class,
            ],
        ],
        'passwords' => [
            'users' => [
                'provider' => 'users',
                'table' => 'password_resets',
                'expire' => 60,
                'throttle' => 60,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the session settings for your application. By
    | default, we'll use the native PHP session driver. You may specify
    | other drivers if you need to, such as database or Redis.
    */
    'session' => [
        'driver' => 'file',
        'lifetime' => 120,
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => __DIR__.'/../../storage/framework/sessions',
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'terrafusion_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the cache settings for your application. The
    | built-in file cache driver is used by default, but you may use any
    | of the other cache drivers available in the application.
    */
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => __DIR__.'/../../storage/framework/cache/data',
            ],
        ],
        'prefix' => 'terrafusion_cache',
    ],

    /*
    |--------------------------------------------------------------------------
    | View Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the view settings for your application. Views
    | are used to render the HTML for your application's web pages.
    */
    'view' => [
        'paths' => [
            __DIR__.'/../../resources/views',
        ],
        'compiled' => __DIR__.'/../../storage/framework/views',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage
    |--------------------------------------------------------------------------
    |
    | Here you may configure the default file storage disk for your
    | application. You may also configure the root URL for file storage.
    */
    'filesystems' => [
        'default' => 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => __DIR__.'/../../storage/app',
            ],
            'public' => [
                'driver' => 'local',
                'root' => __DIR__.'/../../storage/app/public',
                'url' => getenv('APP_URL').'/storage',
                'visibility' => 'public',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Here you may specify which proxies should be trusted to set the
    | X-Forwarded-* headers which are needed for some load balancers.
    */
    'trusted_proxies' => [
        '127.0.0.1',
        'localhost',
    ],
];
