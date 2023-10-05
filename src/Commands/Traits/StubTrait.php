<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands\Traits;

use Addonize\ExtensionManager\Extension;
use Addonize\ExtensionManager\Support\Config\GenerateConfigReader;
use Addonize\ExtensionManager\Support\Json;
use Addonize\ExtensionManager\Support\Stub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @method argument(string $string)
 */
trait StubTrait
{
    protected bool $runningAsRootDir = false;
    protected $buildClassName = null;
    protected Extension $extension;

    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        if (! is_file($path)) {
            return;
        }

        $content = file_get_contents($path);
        if (! str_contains($content, $replace)) {
            file_put_contents($path, str_replace($search, $replace, $content));
        }
    }

    public function getExtensionJsonReplaceContent($provider, $extensionKey): string
    {
        $class = sprintf('Plugins\\%s\\Providers\\%s', $extensionKey, $provider);
        return str_replace('\\', '\\\\', $class);
    }

    public function getExtensionJsonSearchContent($extensionKey): string
    {
        $class = sprintf('Plugins\\%s\\Providers\\%sServiceProvider', $extensionKey, $extensionKey);
        return str_replace('\\', '\\\\', $class);
    }

    /**
     * Install the provider in the plugin.json file.
     *
     * @param string $after
     * @param string $name
     * @param string $extensionJsonPath
     */
    protected function installExtensionProviderAfter(string $after, string $name, string $extensionJsonPath): void
    {
        $providers = Str::before(
            Str::after(file_get_contents($extensionJsonPath), '"providers": ['),
            sprintf('],%s    "autoloadFiles"', PHP_EOL)
        );

        if (!Str::contains($providers, $name)) {
            $modifiedProviders = str_replace(
                sprintf('"%s",', $after),
                sprintf('"%s",', $after).PHP_EOL.'        '.sprintf('"%s",', $name),
                $providers,
            );

            $this->replaceInFile(
                $providers,
                $modifiedProviders,
                $extensionJsonPath,
            );
        }
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('extKey'));
    }

    protected function buildClass($extKey): string
    {
        $this->runningAsRootDir = false;
        if (str_starts_with($extKey, 'App')) {
            $this->runningAsRootDir = true;
            $this->buildClassName = $extKey;
        }

        return $this->getStubContents($this->getStub());
    }

    protected function getPath($extKey): string
    {
        $path = parent::getPath($extKey);

        $this->type = $path;

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace): mixed
    {
        return $rootNamespace;
    }

    protected function getStubName(): ?string
    {
        return null;
    }

    /**
     * implement from \Illuminate\Console\GeneratorCommand.
     *
     * @see \Illuminate\Console\GeneratorCommand
     */
    protected function getStub(): string
    {
        $stubName = $this->getStubName();
        if (! $stubName) {
            throw new RuntimeException('Please provider stub extKey in getStubName method');
        }

        $baseStubPath = base_path(sprintf("stubs/%s.stub", $stubName));
        if (file_exists($baseStubPath)) {
            return $baseStubPath;
        }

        $stubPath = dirname(__DIR__). sprintf("/stubs/%s.stub", $stubName);
        if (file_exists($stubPath)) {
            return $stubPath;
        }

        throw new RuntimeException(sprintf("stub path does not exists: %s", $stubPath));
    }

    /**
     * Get class name.
     */
    public function getClass(): string
    {
        return class_basename($this->argument('extKey'));
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param $stubPath
     * @return string
     */
    protected function getStubContents($stubPath): string
    {
        $method = sprintf('get%sStubPath', Str::studly(strtolower($stubPath)));

        // custom stubPath
        if (method_exists($this, $method)) {
            $stubFilePath = $this->$method();
        } else {
            // run in command: addonize new Xxx
            $stubFilePath = dirname(__DIR__). sprintf("/stubs/%s.stub", $stubPath);

            $stubFilePath = file_exists($stubFilePath) ? $stubFilePath : $stubPath;
        }

        $mimeType = File::mimeType($stubFilePath);
        if (
            str_contains($mimeType, 'application/')
            || str_contains($mimeType, 'text/')
        ) {
            $stubFile = new Stub($stubFilePath, $this->getReplacement($stubFilePath));
            $content = $stubFile->render();
        } else {
            $content = File::get($stubFilePath);
        }

        // format json style
        if (str_contains($stubPath, 'json')) {
            return Json::make()->decode($content)->encode();
        }

        return $content;
    }

    public function getReplaceKeys($content): ?array
    {
        preg_match_all('/(\$[^\s.]*?\$)/', $content, $matches);

        return $matches[1] ?? [];
    }

    public function getReplacesByKeys(array $keys): ?array
    {
        $replaces = [];
        foreach ($keys as $key) {
            $currentReplacement = str_replace('$', '', $key);

            $currentReplacementLower = Str::of($currentReplacement)->lower()->toString();
            $method = sprintf('get%sReplacement', Str::studly($currentReplacementLower));

            if (method_exists($this, $method)) {
                $replaces[$currentReplacement] = $this->$method();
            } else {
                info($currentReplacement.' does match any replace content');
                // keep origin content
                $replaces[$currentReplacement] = $key;
            }
        }

        return $replaces;
    }

    public function getReplacedContent(string $content, array $keys = []): string
    {
        if (! $keys) {
            $keys = $this->getReplaceKeys($content);
        }

        $replaces = $this->getReplacesByKeys($keys);

        return str_replace($keys, $replaces, $content);
    }

    /**
     * Get array replacement for the specified stub.
     *
     * @param $stubPath
     * @return array
     */
    protected function getReplacement($stubPath): array
    {
        if (! file_exists($stubPath)) {
            throw new RuntimeException("stubPath $stubPath not exists");
        }

        $stubContent = @file_get_contents($stubPath);

        $keys = $this->getReplaceKeys($stubContent);

        return $this->getReplacesByKeys($keys);
    }

    /**
     * @return string|bool
     */
    public function getAuthorsReplacement(): string|bool
    {
        return Json::make()->encode(config('extensions.composer.author'));
    }

    public function getAuthorNameReplacement(): mixed
    {
        $authors = config('extensions.composer.author');
        if (count($authors)) {
            return $authors[0]['name'] ?? 'Extension Manager';
        }

        return 'Extension Manager';
    }

    public function getAuthorLinkReplacement(): mixed
    {
        $authors = config('extensions.composer.author');
        if (count($authors)) {
            return $authors[0]['homepage'] ?? 'https://foo.com';
        }

        return 'https://foo.com';
    }

    /**
     * Get namespace for plugin service provider.
     */
    protected function getNamespaceReplacement(): string
    {
        if ($this->runningAsRootDir) {
            return Str::beforeLast($this->buildClassName, '\\');
        }

        $namespace = $this->extension->getClassNamespace();
        $namespace = $this->getDefaultNamespace($namespace);

        return str_replace('\\\\', '\\', $namespace);
    }

    public function getClassReplacement(): string
    {
        return $this->getClass();
    }

    /**
     * Get the plugin extKey in lower case.
     */
    protected function getLowerNameReplacement(): string
    {
        return $this->extension->getLowerName();
    }

    /**
     * Get the plugin extKey in studly case.
     */
    protected function getStudlyNameReplacement(): string
    {
        return $this->extension->getStudlyName();
    }

    /**
     * Get the plugin extKey in studly case.
     */
    protected function getSnakeNameReplacement(): string
    {
        return $this->extension->getSnakeName();
    }

    /**
     * Get the plugin extKey in kebab case.
     */
    protected function getKebabNameReplacement(): string
    {
        return $this->extension->getKebabName();
    }

    /**
     * Get replacement for $VENDOR$.
     */
    protected function getVendorReplacement(): string
    {
        return $this->extension->config('composer.vendor');
    }

    /**
     * Get replacement for $PLUGIN_NAMESPACE$.
     */
    protected function getExtensionNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', $this->extension->config('namespace'));
    }

    protected function getProviderNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', GenerateConfigReader::read('provider')->getNamespace());
    }

    public function __get($extKey): Extension
    {
        if ($extKey === 'plugin') {
            // get Plugin extKey from Namespace: Plugin\DemoTest => DemoTest
            $namespace = str_replace('\\', '/', app()->getNamespace());
            $namespace = rtrim($namespace, '/');
            $extensionKey = basename($namespace);

            // when running in rootDir
            if ($extensionKey == 'App') {
                $extensionKey = null;
            }

            if (empty($this->extension)) {
                $this->extension = new Extension($extensionKey);
            }

            return $this->extension;
        }

        throw new RuntimeException("unknown property $extKey");
    }
}
