<?php

use Illuminate\Support\Facades\Facade;

!defined('DEV_MODE') && define('DEV_MODE', env('DEV_MODE', false));

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Asia/Riyadh'),
    'datetime_format' => 'Y-m-d h:i:s a',
    'date_format'     => 'Y-m-d',
    'time_format'     => 'h:i:s a',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'ar'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'locales' => [
	    'en' => 'English',
	    'ar' => 'العربية',
    ],

    'available_locales' => [ 'ar' => 'ar', 'en' => 'en' ],

    'perPage'  => 10,
    'per_page' => 15,

    'faker_locale' => env('APP_FAKER_LOCALE', 'ar_SA'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'dev_mode' => DEV_MODE,

    'developer' => env('DEVELOPER', null),

    'media_collection_hero_image_name' => env('MEDIA_COLLECTION_HERO_IMAGE_NAME', 'hero_image'),

    'media_collection_multiple_image_name' => env('MEDIA_COLLECTION_MULTIPLE_IMAGE_NAME', 'media_images'),
    'media_collection_single_image_name'   => env('MEDIA_COLLECTION_SINGLE_IMAGE_NAME', 'media_images'),

    'media_collection_multiple_file_name' => env('MEDIA_COLLECTION_MULTIPLE_FILE_NAME', 'files'),
    'media_collection_single_file_name'   => env('MEDIA_COLLECTION_SINGLE_FILE_NAME', 'file'),

    'media_collection_thumb'     => 'preview',
    'media_collection_full-size' => 'full-size',

    'media_collection_name'              => env('MEDIA_COLLECTION_SINGLE_IMAGE_NAME', 'files'),
    'media_collection_single_page_image' => env('MEDIA_COLLECTION_SINGLE_PAGE_IMAGE', 'page_image'),
];
