<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager;

use Addonize\ExtensionManager\Manager\FileManager;
use Addonize\ExtensionManager\Support\Json;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Extension
{
    protected string $extensionKey;

    /**
     * @var FileManager
     */
    protected FileManager $manager;

    public function __construct(?string $extensionKey = null)
    {
        $this->manager = new FileManager();

        $this->setExtensionKey($extensionKey);
    }

    public function config(string $key, $default = null)
    {
        return config('Extensions.'.$key, $default);
    }

    public function setExtensionKey(?string $extensionKey = null): void
    {
        $this->extensionKey = $extensionKey;
    }

    public function getExtKey(): string
    {
        return $this->getStudlyName();
    }

    public function getLowerName(): string
    {
        return Str::lower($this->extensionKey);
    }

    public function getStudlyName(): string
    {
        return Str::studly($this->extensionKey);
    }

    public function getKebabName(): string
    {
        return Str::kebab($this->extensionKey);
    }

    public function getSnakeName(): string
    {
        return Str::snake($this->extensionKey);
    }

    public function getClassNamespace(): string
    {
        $namespace = $this->config('namespace');
        $namespace .= '\\'.$this->getStudlyName();
        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    public function getSeederNamespace(): ?string
    {
        return "{$this->getClassNamespace()}\\Database\Seeders\\";
    }

    public function getExtensionPath(): ?string
    {
        $path = $this->config('paths.plugins');
        $extensionKey = $this->getStudlyName();

        return sprintf("%s/%s", $path, $extensionKey);
    }

    public function getFactoryPath(): string
    {
        $path = $this->getExtensionPath();

        return sprintf("%s/database/factories", $path);
    }

    public function getMigratePath(): string
    {
        $path = $this->getExtensionPath();

        return sprintf("%s/database/migrations", $path);
    }

    public function getSeederPath(): ?string
    {
        $path = $this->getExtensionPath();

        return sprintf("%s/database/seeders", $path);
    }

    public function getAssetsPath(): ?string
    {
        if (!$this->exists()) {
            return null;
        }

        $path = $this->config('paths.assets');
        $extensionKey = $this->getStudlyName();

        return sprintf("%s/%s", $path, $extensionKey);
    }

    public function getAssetsSourcePath(): ?string
    {
        if (!$this->exists()) {
            return null;
        }

        $path = $this->getExtensionPath();

        return sprintf("%s/resources/assets", $path);
    }

    public function getComposerJsonPath(): ?string
    {
        $path = $this->getExtensionPath();

        return sprintf("%s/composer.json", $path);
    }

    public function getExtensionJsonPath(): ?string
    {
        $path = $this->getExtensionPath();

        return sprintf("%s/Extension.json", $path);
    }

    public function install(): bool
    {
        return $this->manager->install($this->getStudlyName());
    }

    public function activate(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        return $this->manager->activate($this->getStudlyName());
    }

    public function deactivate(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        return $this->manager->deactivate($this->getStudlyName());
    }

    public function uninstall(): bool
    {
        return $this->manager->uninstall($this->getStudlyName());
    }

    public function isActivate(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        return $this->manager->isActivate($this->getStudlyName());
    }

    public function isDeactivate(): bool
    {
        return !$this->isActivate();
    }

    public function exists(): bool
    {
        if (!$extensionKey = $this->getStudlyName()) {
            return false;
        }

        if (in_array($extensionKey, $this->all())) {
            return true;
        }

        return false;
    }

    public function all(): array
    {
        $path = $this->config('paths.Extensions');
        $ExtensionJsons = File::glob("$path/**/Extension.json");

        $Extensions = [];
        foreach ($ExtensionJsons as $ExtensionJson) {
            $extensionKey = basename(dirname($ExtensionJson));

            if (!$this->isValidExtension($extensionKey)) {
                continue;
            }

            if (!$this->isAvailableExtension($extensionKey)) {
                continue;
            }

            $Extensions[] = $extensionKey;
        }

        return $Extensions;
    }

    public function isValidExtension(?string $extensionKey = null): bool
    {
        if (!$extensionKey) {
            $extensionKey = $this->getStudlyName();
        }

        if (!$extensionKey) {
            return false;
        }

        $path = $this->config('paths.Extensions');

        $ExtensionJsonPath = sprintf('%s/%s/Extension.json', $path, $extensionKey);

        $ExtensionJson = Json::make($ExtensionJsonPath);

        return $extensionKey == $ExtensionJson->get('extKey');
    }

    public function isAvailableExtension(?string $extensionKey = null): bool
    {
        if (!$extensionKey) {
            $extensionKey = $this->getStudlyName();
        }

        if (!$extensionKey) {
            return false;
        }

        try {
            // Verify that the program is loaded correctly by loading the program
            $Extension = new Extension($extensionKey);
            $Extension->manualAddNamespace();

            $serviceProvider = sprintf('%s\\Providers\\%sServiceProvider', $Extension->getClassNamespace(), $extensionKey);

            return class_exists($serviceProvider);
        } catch (\Throwable $e) {
            \info("{$extensionKey} registration failed, not a valid Extension: ".$e->getMessage());

            return false;
        }

        return true;
    }

    public function allActivate(): array
    {
        return array_keys(array_filter($this->manager->all()));
    }

    public function allDeactivate(): array
    {
        return array_diff($this->all(), $this->allActivate());
    }

    public function registerFiles(): void
    {
        $path = $this->getExtensionPath();

        $files = Json::make($this->getExtensionJsonPath())->get('autoloadFiles', []);
        foreach ($files as $file) {
            if (!is_string($file)) {
                continue;
            }

            $filepath = "$path/$file";
            if (is_file($filepath)) {
                include_once $filepath;
            }
        }
    }

    public function registerProviders(): void
    {
        $providers = Json::make($this->getExtensionJsonPath())->get('providers', []);

        (new \Illuminate\Foundation\ProviderRepository(app(), app('files'), $this->getCachedServicesPath()))
            ->load($providers);
    }

    public function registerAliases(): void
    {
        $aliases = Json::make($this->getExtensionJsonPath())->get('aliases', []);

        $loader = AliasLoader::getInstance();
        foreach ($aliases as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    public function getCachedServicesPath(): string
    {
        // This checks if we are running on a Laravel Vapor managed instance
        // and sets the path to a writable one (services path is not on a writable storage in Vapor).
        if (!is_null(env('VAPOR_MAINTENANCE_MODE', null))) {
            return Str::replaceLast('config.php', $this->getSnakeName().'_extension.php', app()->getCachedConfigPath());
        }

        return Str::replaceLast('services.php', $this->getSnakeName().'_extension.php', app()->getCachedServicesPath());
    }

    public function manualAddNamespace(): void
    {
        $extKey = $this->getStudlyName();
        if (!$extKey) {
            return;
        }

        if (file_exists(base_path('/vendor/autoload.php'))) {
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require base_path('/vendor/autoload.php');

            $namespaces = config('extensions.namespaces', []);

            foreach ($namespaces as $namespace => $paths) {
                $appPaths = array_map(function ($path) use ($extKey) {
                    return sprintf("%s/%s/app", $path, $extKey);
                }, $paths);
                $loader->addPsr4(sprintf("%s\\%s\\", $namespace, $extKey), $appPaths, true);

                $factoryPaths = array_map(function ($path) use ($extKey) {
                    return sprintf("%s/%s/database/factories", $path, $extKey);
                }, $paths);
                $loader->addPsr4(sprintf("%s\\%s\\Database\\Factories\\", $namespace, $extKey), $factoryPaths, true);

                $seederPaths = array_map(function ($path) use ($extKey) {
                    return sprintf("%s/%s/database/seeders", $path, $extKey);
                }, $paths);
                $loader->addPsr4(sprintf("%s\\%s\\Database\\Seeders\\", $namespace, $extKey), $seederPaths, true);

                $testPaths = array_map(function ($path) use ($extKey) {
                    return sprintf("%s/%s/tests", $path, $extKey);
                }, $paths);
                $loader->addPsr4(sprintf("%s\\%s\\Tests\\", $namespace, $extKey), $testPaths, true);
            }
        }
    }

    public function getExtensionInfo(): array
    {
        // Validation: Does the directory name and extKey match correctly
        // Available: Whether the service provider is registered successfully
        $item['Extension extKey'] = "<info>{$this->getStudlyName()}</info>";
        $item['Validation'] = $this->isValidExtension() ? '<info>true</info>' : '<fg=red>false</fg=red>';
        $item['Available'] = $this->isAvailableExtension() ? '<info>Available</info>' : '<fg=red>Unavailable</fg=red>';
        $item['Extension Status'] = $this->isActivate() ? '<info>Activate</info>' : '<fg=red>Deactivate</fg=red>';
        $item['Assets Status'] = file_exists($this->getAssetsPath()) ? '<info>Published</info>' : '<fg=red>Unpublished</fg=red>';
        $item['Extension Path'] = $this->replaceDir($this->getExtensionPath());
        $item['Assets Path'] = $this->replaceDir($this->getAssetsPath());

        return $item;
    }

    public function replaceDir(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return ltrim(str_replace(base_path(), '', $path), '/');
    }

    public function __toString()
    {
        return $this->getStudlyName();
    }
}
