# Development

## Generate Extension Command

Generate the given console command for the specified extension.

```php
manage make:command CreateDemoCommand
```

## Generate Extension Migration

Generate a migration for specified extension.

```php
manage make:migration create_demos_table
```

## Generate Extension Seed

Generate the given seed name for the specified extension.

```php
manage make:seed seed_fake_demos
```

## Generate Extension Factory

Generate the given database factory for the specified extension.

```php
manage make:factory FactoryName
```

## Generate Extension Provider

Generate the given service provider name for the specified extension.

```php
manage make:provider DemoServiceProvider
```

## Generate Extension Controller

Generate a controller for the specified extension.

```php
manage make:controller PostsController
```

## Generate Extension Model

Generate the given model for the specified extension.

```php
manage make:model Post
```

Optional options:

- `--fillable=field1,field2`: set the fillable fields on the generated model
- `--migration`, `-m`: create the migration file for the given model

## Generate Extension Middleware

Generate the given middleware name for the specified extension.

```php
manage make:middleware CanReadPostsMiddleware
```

## Generate Extension DTO

Generate a [DTO(data transfer object)](../dto/) for specified extension.

```php
manage make:dto VerifySignDTO
```

## Generate Extension Mail

Generate the given mail class for the specified extension.

```php
manage make:mail SendWeeklyPostsEmail
```

## Generate Extension Notification

Generate the given notification class name for the specified extension.

```php
manage make:notification NotificationAdminOfNewComment
```

## Generate Extension Listener

Generate the given listener for the specified extension. Optionally you can specify which event class it should listen to. It also accepts a `--queued` flag allowed queued event listeners.

```php
manage make:listener NotificationUsersOfANewPost

manage make:listener NotificationUsersOfANewPost --event=PostWasCreated

manage make:listener NotificationUsersOfANewPost --event=PostWasCreated --queued
```

## Generate Extension Request

Generate the given request for the specified extension.

```php
manage make:request CreatePostRequest
```

## Generate Extension Event

Generate the given event for the specified extension.

```php
manage make:event BlogPostWasUpdated
```

## Generate Extension Job

Generate the given job for the specified extension.

```php
manage make:job JobName

//A synchronous job class
manage make:job JobName --sync
```

## Generate Extension Policy

Generate the given policy class for the specified extension.

The `Policies` is not generated by default when creating a new extension. Change the value of `paths.generator.policies` in `extensions.php` to your desired location.

```php
manage make:policy PolicyName
```

## Generate Extension Rule

Generate the given validation rule class for the specified extension.

The `Rules` folder is not generated by default when creating a new extension. Change the value of `paths.generator.rules` in `extensions.php` to your desired location.

```php
manage make:rule ValidationRule
```

## Generate Extension Resource

Generate the given resource class for the specified extension. It can have an optional --collection argument to generate a resource collection.

The `Transformers` folder is not generated by default when creating a new extension. Change the value of `paths.generator.resource` in `extensions.php` to your desired location.

```php
manage make:resource PostResource

manage make:resource PostResource --collection
```

## Generate Extension Test

Generate the given test class for the specified extension.

```php
manage make:test EloquentPostRepositoryTest
```

## Generate Extension Schedule Provider

Generate a console service provider for specified extension.

```php
manage make:console-provider
```

## Generate Extension Event Provider

Generate a event provider for specified extension.

```php
manage make:event-provider
```

- You need to add it yourself to the `providers` parameter of `extension.json`.

## Generate Extension SQL Provider

Generate a sql provider for specified extension.

```php
manage make:sql-provider
```

- You need to add it yourself to the `providers` parameter of `extension.json`.

## Generate Extension Command Word Provider

Generate a [cmd word service provider](../command-word/) for specified extension.

```php
manage make:cmdword-provider
```

- You need to add it yourself to the `providers` parameter of `extension.json`.
