<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value determines the default "server" that will be used by Octane
    | when starting, restarting, or stopping your application server.
    | You are free to change this to the server you prefer.
    |
    | Supported: "swoole", "roadrunner"
    |
    */

    'server' => env('OCTANE_SERVER', 'swoole'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When this configuration value is set to "true", Octane will inform the
    | framework that all absolute URLs should be generated using the HTTPS
    | protocol. Otherwise your URLs may be generated using HTTP.
    |
    */

    'https' => env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Octane Listeners
    |--------------------------------------------------------------------------
    |
    | All of the event listeners for Octane's events are defined below. These
    | listeners are responsible for resetting your application's state after
    | each request. You may even add your own listeners to this array.
    |
    */

    'listeners' => [
        \Laravel\Octane\Listeners\WorkerStarting::class => [
            \Laravel\Octane\Listeners\EnsureUploadedFilesAreValid::class,
            \Laravel\Octane\Listeners\EnsureUploadedFilesCanBeMoved::class,
        ],

        \Laravel\Octane\Listeners\RequestReceived::class => [
            //
        ],

        \Laravel\Octane\Listeners\RequestHandled::class => [
            \Laravel\Octane\Listeners\FlushLogContext::class,
            \Laravel\Octane\Listeners\FlushLocaleState::class,
            \Laravel\Octane\Listeners\FlushSessionState::class,
            \Laravel\Octane\Listeners\FlushAuthenticationState::class,
            \Laravel\Octane\Listeners\FlushValidationState::class,
            \Laravel\Octane\Listeners\DisconnectFromDatabases::class,
            \Laravel\Octane\Listeners\CollectGarbage::class,
            
            // Custom listeners for NFC app (disabled for local testing)
            // \App\Listeners\Octane\FlushNfcAnalyticsState::class,
            // \App\Listeners\Octane\FlushUploadState::class,
        ],

        \Laravel\Octane\Listeners\TaskReceived::class => [
            //
        ],

        \Laravel\Octane\Listeners\TaskHandled::class => [
            \Laravel\Octane\Listeners\FlushLogContext::class,
            \Laravel\Octane\Listeners\CollectGarbage::class,
        ],

        \Laravel\Octane\Listeners\TickReceived::class => [
            //
        ],

        \Laravel\Octane\Listeners\TickHandled::class => [
            \Laravel\Octane\Listeners\FlushLogContext::class,
            \Laravel\Octane\Listeners\CollectGarbage::class,
        ],

        \Laravel\Octane\Listeners\WorkerErrorOccurred::class => [
            \Laravel\Octane\Listeners\ReportException::class,
            \Laravel\Octane\Listeners\StopWorkerIfNecessary::class,
        ],

        \Laravel\Octane\Listeners\WorkerStopping::class => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Warm / Flush Bindings
    |--------------------------------------------------------------------------
    |
    | The bindings listed below will either be pre-warmed when a worker boots
    | or they will be flushed before every request. Flushing a service will
    | force the container to resolve that service again on the next request.
    |
    */

    'warm' => [
        'auth',
        'cache',
        'cache.store',
        'config',
        'encrypter',
        'db',
        'db.connection',
        'hash',
        'log',
        'queue',
        'queue.connection',
        'router',
        'session',
        'session.store',
        'translator',
        'url',
        'view',
        // NFC specific services
        \App\Helpers\ThemeHelper::class,
    ],

    'flush' => [
        'auth.driver',
        'cache.store',
        'session',
        'session.store',
        // NFC specific flushes
        'nfc.upload.manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Cache Table
    |--------------------------------------------------------------------------
    |
    | While using Swoole, you may leverage the Octane cache, which is powered
    | by a Swoole table. You may set the maximum number of rows as well as
    | the number of bytes per row using the configuration options below.
    |
    */

    'cache' => [
        'rows' => 1000,
        'bytes' => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Swoole Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure some of the Swoole server options, including the
    | server host, port, and the number of workers. This configuration is
    | only applicable when using Swoole to serve your application.
    |
    */

    'swoole' => [
        'options' => [
            'log_file' => storage_path('logs/swoole_http.log'),
            'package_max_length' => 10 * 1024 * 1024, // 10MB para uploads NFC
            'buffer_output_size' => 32 * 1024 * 1024, // 32MB
            'socket_buffer_size' => 128 * 1024 * 1024, // 128MB
            'max_request' => 1000, // Restart worker cada 1000 requests (memory leak prevention)
            'reload_async' => true,
            'max_wait_time' => 60,
            'enable_reuse_port' => true,
            'enable_coroutine' => true,
            'send_yield' => true,
            'max_coroutine' => 3000,
            
            // NFC specific optimizations
            'upload_tmp_dir' => storage_path('app/octane-uploads'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | RoadRunner Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure some of the RoadRunner server options, including
    | the server host and port. This configuration is only applicable when
    | using RoadRunner to serve your application. Note that you must have
    | the RoadRunner binary installed on the server where your app will run.
    |
    */

    'roadrunner' => [
        'binary_path' => env('RR_BINARY_PATH', '/usr/local/bin/rr'),
    ],

];