# Overview

## Usage

```php
php artisan addonize                  // Enter Extension Development Mode

addonize extension                    // View All Commands
addonize extension:list               // View All Installed Extensions
addonize new                          // Generate A New Extension
addonize enter                        // Go to Extension directory
addonize back                         // Back to the addonize root directory
```

## Development

```php
addonize make:command                 // Generate Extension Command
addonize make:migration               // Generate Extension Migration
addonize make:seed                    // Generate Extension Seed
addonize make:factory                 // Generate Extension Factory
addonize make:provider                // Generate Extension Provider
addonize make:controller              // Generate Extension Controller
addonize make:model                   // Generate Extension Model
addonize make:middleware              // Generate Extension Middleware
addonize make:dto                     // Generate Extension DTO (addonize/dto)
addonize make:mail                    // Generate Extension Mail
addonize make:notification            // Generate Extension Notification
addonize make:listener                // Generate Extension Listener
addonize make:request                 // Generate Extension Request
addonize make:event                   // Generate Extension Event
addonize make:job                     // Generate Extension Job
addonize make:policy                  // Generate Extension Policy
addonize make:rule                    // Generate Extension Rule
addonize make:resource                // Generate Extension Resource
addonize make:test                    // Generate Extension Test
addonize make:schedule-provider       // Generate Extension Schedule Provider
addonize make:event-provider          // Generate Extension Event Provider
addonize make:sql-provider            // Generate Extension SQL Provider
addonize make:cmdword-provider        // Generate Extension Command Word Provider (addonize/cmd-word-addonizer)
```

## Control

### addonize mode

```php
addonize extension:unzip                 // Unzip the extension package to the extension directory: /extensions/extensions/{extKey}/
addonize extension:publish               // Publish Extension (static resources): /public/assets/extensions/{extKey}/
addonize extension:unpublish             // Unpublish (remove static resources)
addonize extension:composer-update       // Update Extension Composer Package
addonize extension:migrate               // Run Extension Migrate
addonize extension:migrate-rollback      // Rollback Extension Migrate
addonize extension:migrate-refresh       // Refresh Extension Migrate
addonize extension:migrate-reset         // Reset Extension Migrate
addonize extension:seed                  // Run Extension Seed
addonize extension:install               // Install Extension (Run the unzip/publish/composer-update/migrate command in sequence)
addonize extension:uninstall             // Uninstall Extension
```

### artisan mode

```php
php artisan extension:unzip            // Unzip the extension package to the extension directory: /extensions/extensions/{extKey}/
php artisan extension:publish          // Publish Extension (static resources): /public/assets/extensions/{extKey}/
php artisan extension:unpublish        // Unpublish (remove static resources)
php artisan extension:composer-update  // Update Extension Composer Package
php artisan extension:migrate          // Run Extension Migrate
php artisan extension:migrate-rollback // Rollback Extension Migrate
php artisan extension:migrate-refresh  // Refresh Extension Migrate
php artisan extension:migrate-reset    // Reset Extension Migrate
php artisan extension:seed             // Run Extension Seed
php artisan extension:install          // Install Extension (Run the unzip/publish/composer-update/migrate command in sequence)
php artisan extension:uninstall        // Uninstall Extension
```

## Management

### management mode

```php
php artisan extension:activate         // Activate Extension
php artisan extension:deactivate       // Deactivate Extension
```

### artisan mode

```php
addonize extension:activate            // Activate Extension
addonize extension:deactivate          // Deactivate Extension
```
