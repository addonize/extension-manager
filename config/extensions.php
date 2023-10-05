<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

return [
    'namespace' => $extensionsNamespace = 'Extensions',

    // YOU COULD CUSTOM HERE
    'namespaces' => [
        $extensionsNamespace => [
            base_path('addons/extensions'),
        ],
    ],

    'autoload_files' => [
        base_path('vendor/addonize/extension-manager/src/Helpers.php'),
    ],

    'merge_plugin_config' => [
        'include' => [
            ltrim(str_replace(base_path(), '', base_path('addons/extensions/*/composer.json')), '/'),
        ],
        'recurse' => true,
        'replace' => false,
        'ignore-duplicates' => false,
        'merge-dev' => true,
        'merge-extra' => true,
        'merge-extra-deep' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | YOU COULD CUSTOM HERE
    |
    */
    'composer'  => [
        'vendor' => 'addonize',
        'author' => [
            [
                'name'  => 'Ifeoluwa Adewunmi',
                'email' => 'ifeoluwa.adewunmi94@gmail.com',
                'homepage' => 'https://github.com/ife-adewunmi',
                'role' => 'Creator',
            ],
        ],
    ],

    'paths' => [
        'base' => base_path('addons'),
        'backups' => base_path('addons/backups/extensions'),
        'extensions' => base_path('addons/extensions'),
        'assets' => public_path('assets/extensions'),
        'migration' => base_path('database/migrations'),

        'generator' => [
            'config'            => ['path' => 'config', 'generate' => true],
            'command'           => ['path' => 'app/Console', 'generate' => false],
            'controller'        => ['path' => 'app/Http/Controllers', 'generate' => false],
            'filter'            => ['path' => 'app/Http/Middleware', 'generate' => false],
            'request'           => ['path' => 'app/Http/Requests', 'generate' => false],
            'resource'          => ['path' => 'app/Http/Resources', 'generate' => false],
            'model'             => ['path' => 'app/Models', 'generate' => true],
            'provider'          => ['path' => 'app/Providers', 'generate' => true],
            'policies'          => ['path' => 'app/Policies', 'generate' => false],
            'repository'        => ['path' => 'app/Repositories', 'generate' => false],
            'event'             => ['path' => 'app/Events', 'generate' => false],
            'listener'          => ['path' => 'app/Listeners', 'generate' => false],
            'rules'             => ['path' => 'app/Rules', 'generate' => false],
            'jobs'              => ['path' => 'app/Jobs', 'generate' => false],
            'emails'            => ['path' => 'app/Mail', 'generate' => false],
            'notifications'     => ['path' => 'app/Notifications', 'generate' => false],
            'migration'         => ['path' => 'database/migrations', 'generate' => true],
            'seeder'            => ['path' => 'database/seeders', 'generate' => true],
            'factory'           => ['path' => 'database/factories', 'generate' => true],
            'routes'            => ['path' => 'routes', 'generate' => true],
            'assets'            => ['path' => 'resources/assets', 'generate' => true],
            'lang'              => ['path' => 'resources/lang', 'generate' => true],
            'views'             => ['path' => 'resources/views', 'generate' => true],
            'test'              => ['path' => 'tests/Unit', 'generate' => true],
            'test-feature'      => ['path' => 'tests/Feature', 'generate' => true],
        ],
    ],

    'stubs' => [
        'path'         => dirname(__DIR__).'/src/Commands/stubs',
        'files'        => [
            'controller.web'        => 'app/Http/Controllers/$STUDLY_NAME$SettingController.php',
            'scaffold/provider'     => 'app/Providers/$STUDLY_NAME$ServiceProvider.php',
            'route-provider'        => 'app/Providers/RouteServiceProvider.php',
            'command-provider'      => 'app/Providers/CommandServiceProvider.php',
            'exception-provider'    => 'app/Providers/ExceptionServiceProvider.php',
            'scaffold/config'       => 'config/$KEBAB_NAME$.php',
            'init_extension_config' => 'database/migrations/init_$SNAKE_NAME$_config.php',
            'seeder'                => 'database/seeders/DatabaseSeeder.php',
            'assets/css/app'        => 'resources/assets/css/app.css',
            'assets/js/app'         => 'resources/assets/js/app.js',
            'assets/js/bootstrap'   => 'resources/assets/js/bootstrap.js',
            'views/layouts/master'  => 'resources/views/layouts/master.blade.php',
            'views/layouts/header'  => 'resources/views/layouts/header.blade.php',
            'views/layouts/footer'  => 'resources/views/layouts/footer.blade.php',
            'views/layouts/tips'    => 'resources/views/layouts/tips.blade.php',
            'views/app'             => 'resources/views/app.blade.php',
            'views/index'           => 'resources/views/index.blade.php',
            'views/setting'         => 'resources/views/setting.blade.php',
            'routes/web'            => 'routes/web.php',
            'routes/api'            => 'routes/api.php',
            'vite.config'           => 'vite.config.js',
            'package.json'          => 'package.json',
            'composer.json'         => 'composer.json',
            'extension.json'        => 'extension.json',
            'gitignore'             => '.gitignore',
            'readme'                => 'README.md',
        ],
        'gitkeep'      => true,
    ],

    'manager' => [
        'default' => [
            'file' => base_path('addonize.json'),
        ],
    ],
];
