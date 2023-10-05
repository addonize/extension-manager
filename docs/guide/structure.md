# Extension Structure

## Directory Structure

```php
laravel/            // Main Program
└── addons/         // Addons directory (addons and themes)
    └── extensions/            // Extension directory
        └── DemoExtension/         // Demo Extension
            ├── app/
            ├── config/
            ├── database/
            ├── resources/
            │   ├── assets/
            │   │   ├── images/
            │   │   ├── js/
            │   │   └── css/
            │   ├── lang/
            │   └── views/
            ├── routes/
            ├── tests/
            ├── extension.json
            └── composer.json
```

## extension.json

```json
{
    "extKey": "DemoExtension",
    "name": "Demo Extension",
    "description": "Extension description",
    "developer": "Ifeoluwa Adewunmi",
    "website": "https://github.com/ife-adewunmi",
    "version": "1.0.0",
    "providers": [
        "Extensions\\DemoExtension\\Providers\\DemoExtensionServiceProvider",
        "Extensions\\DemoExtension\\Providers\\CmdWordServiceProvider",
        "Extensions\\DemoExtension\\Providers\\EventServiceProvider"
    ],
    "autoloadFiles": [
        // autoload files
        "app/Http/Function.php"
    ],
    "aliases": {}
}
```

## composer.json

```json
{
    "name": "addonize/demo-extension",
    "license": "Apache-2.0",
    "require": {
        "laravel/email": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Extensions\\DemoExtension\\": "./"
        }
    }
}
```

# Extension Listeners

```php
protected $listen = [
    // extension installing
    'extension:installing' => [
        //
    ],

    // extension installed
    'extension:installed' => [
        // 
    ],

    // extension activating
    'extension:activating' => [
        //
    ],

    // extension activated
    'extension:activated' => [
        //
    ],

    // extension deactivating
    'extension:deactivating' => [
        //
    ],

    // extension deactivated
    'extension:deactivated' => [
        //
    ],

    // extension uninstalling
    'extension:uninstalling' => [
        //
    ],

    // extension uninstalled
    'extension:uninstalled' => [
        //
    ],
];
```

## Assets file publish

Assets are distributed to the public directory when the extension is installed and released.

| Extension Folder                                | Publish to the site resource directory |
|-------------------------------------------------|----------------------------------------|
| /addons/extensions/`{extKey}`/Resources/assets/ | /public/assets/extensions/`{extKey}`/     |
