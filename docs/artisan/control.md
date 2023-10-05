# Control

## Unzip The Extension Package

Unzip the extension files into the `/extensions/` directory, the final directory will be `/extensions/{extKey}/`.

```php
addonize extension:unzip /www/wwwroot/addonize/storage/extensions/123e4567-e89b-12d3-a456-426614174000.zip
```

or

```php
php artisan extension:unzip /www/wwwroot/addonize/storage/extensions/123e4567-e89b-12d3-a456-426614174000.zip
```

## Publish Extension

Publish static resources for the extension `NewExtension`.

```php
addonize extension:publish
```

or

```php
php artisan extension:publish NewExtension
```

- `/extensions/NewExtension/Resources/assets/` Distribute to web directories `/public/assets/extensions/NewExtension/`

## Unpublish

Unpublish static resources for the extension `NewExtension`.

```php
addonize extension:unpublish
```

or

```php
php artisan extension:unpublish NewExtension
```

- `/extensions/NewExtension/Resources/assets/` Distribute to web directories `/public/assets/extensions/NewExtension/`

## Update Extension Composer Package

Composer all extensions.

```php
addonize extension:composer-update
```

or

```php
php artisan extension:composer-update
```

## Run Extension Migrate

Migrate the given extension, or without an extension, an argument, migrate all extensions.

```php
addonize extension:migrate
```

or

```php
php artisan extension:migrate NewExtension
```

## Rollback Extension Migrate

Rollback the given extension, or without an argument, rollback all extensions.

```php
addonize extension:migrate-rollback
```

or

```php
php artisan extension:migrate-rollback NewExtension
```

## Refresh Extension Migrate

Refresh the migration for the given extension, or without a specified extension refresh all extensions migrations.

```php
addonize extension:migrate-refresh
```

or

```php
php artisan extension:migrate-refresh NewExtension
```

## Reset Extension Migrate

Reset the migration for the given extension, or without a specified extension reset all extensions migrations.

```php
addonize extension:migrate-reset
```

or

```php
php artisan extension:migrate-reset NewExtension
```

## Run Extension Seed

Seed the given extension, or without an argument, seed all extensions.

```php
addonize extension:seed
```

or

```php
php artisan extension:seed NewExtension
```

## Install Extension

Execute the `extension:unzip`、`extension:composer-update`、`extension:migrate`、`extension:publish` commands in that order.

```php
addonize extension:install /www/wwwroot/addonize/storage/extensions/123e4567-e89b-12d3-a456-426614174000.zip
```

or

```php
php artisan extension:install /www/wwwroot/addonize/storage/extensions/123e4567-e89b-12d3-a456-426614174000.zip
```

## Uninstall Extension

Uninstall the extension and select whether you want to clean the data of the extension.

```php
addonize extension:uninstall --cleardata=true
addonize extension:uninstall --cleardata=false
```

or

```php
php artisan extension:uninstall NewExtension --cleardata=true
php artisan extension:uninstall NewExtension --cleardata=false
```

- `/extensions/NewExtension/` Physically deletion the folder.
- `/public/assets/extensions/NewExtension/` Physically deletion the folder.
- Remove the extension composer dependency package (skip if the main application or another extension is in use)
- Logically deletion the value of the record where the `extKey` column of the `extensions` table is `NewExtension`.
