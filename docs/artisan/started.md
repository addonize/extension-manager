# Usage Flow

When using plug-in instructions, you need to enable the development mode first, then enter the plug-in directory, and directly use the instructions in the plug-in directory.

## 1. Enable development mode

```php
php artisan manage
```

## 2. Introduce the project path (auto-identify, just enter)

```php
export /path/to/project/vendor/bin
```

## 3. Go to the extension directory

- Create a extension called `NewExtension`

```php
manage new NewExtension
```

- Go to the extension `NewExtension` directory

```php
manage enter NewExtension
```

- Back to the manage root directory

```php
manage back
```

## 4. Execute development

Execute development, management, and control commands in the extension directory
