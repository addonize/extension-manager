# Installation and Setup

## Requirements

| Environment | Requirements |
| --- | --- |
| Laravel | 9.x or 10.x |
| PHP | 8.0 or higher |

## Installation

```bash
composer require addonize/extension-manager
```

## Configuration

### Extension Manager config file

- Publish command

```bash
php artisan vendor:publish --provider="Addonize\ExtensionManager\Providers\PluginServiceProvider"
```

### Main program `composer.json` configuration

> The Extension Manager will automatically add

```json
{
    "extra": {
        "merge-plugin": {
            "include": [
                "addons/extensions/*/composer.json"
                // The windows system is: \\extensions\\plugins\\*\\composer.json
            ],
            "recurse": true,
            "replace": false,
            "ignore-duplicates": false,
            "merge-dev": true,
            "merge-extra": true,
            "merge-extra-deep": true
        }
    },
    "config": {
        "allow-plugins": {
            "wikimedia/composer-merge-plugin": true
        }
    }
}
```

### Directory Structure

```php
laravel/            // Main Program
├── config/             // Configuration file directory
│   └── extensions.php         // Extension config file
├── addons/         // Addons directory
│   ├── extensions/            // Extensions directory
│   └── backups/            // Backup directory
└── addonize.json         // Addon activate and deactivate status
```
