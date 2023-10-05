<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Providers;

use Addonize\ExtensionManager\Extension;
use Addonize\ExtensionManager\Support\Json;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Throwable;

class PluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->autoload();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/extensions.php', 'extensions');
        $this->publishes([
            __DIR__.'/../../config/extensions.php' => config_path('extensions.php'),
        ], 'laravel-plugin-config');

        $this->addMergePluginConfig();

        $this->registerCommands([
            __DIR__.'/../Commands/*',
        ]);
    }

    public function registerCommands($paths): void
    {
        $allCommand = [];

        foreach ($paths as $path) {
            $commandPaths = glob($path);

            foreach ($commandPaths as $command) {
                $commandPath = realpath($command);
                if (! is_file($commandPath)) {
                    continue;
                }

                $commandClass = 'Addonize\\ExtensionManager\\Commands\\'.pathinfo($commandPath, PATHINFO_FILENAME);

                if (class_exists($commandClass)) {
                    $allCommand[] = $commandClass;
                }
            }
        }

        $this->commands($allCommand);
    }

    protected function autoload(): void
    {
        $this->addFiles();

        $extension = new Extension();

        collect($extension->all())->map(function ($extensionKey) {
            try {
                $extension = new Extension($extensionKey);

                if ($extension->isAvailableExtension() && $extension->isActivate()) {
                    $extension->registerFiles();
                    $extension->registerProviders();
                    $extension->registerAliases();
                }
            } catch (Throwable $e) {
                info(sprintf('Extension namespace failed to load ExtKey: %s, reason: %s, file: %s, line: %s',
                    $extensionKey,
                    $e->getMessage(),
                    str_replace(base_path().'/', '', $e->getFile()),
                    $e->getLine(),
                ));
            }
        });
    }

    protected function addFiles(): void
    {
        $files = $this->app['config']->get('extensions.autoload_files');

        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    protected function addMergePluginConfig(): void
    {
        $composerPath = base_path('composer.json');
        $composer = Json::make($composerPath)->get();
        if (! $composer) {
            info('Failed to get base_path("composer.json") content');

            return;
        }

        $userMergePluginConfig = Arr::get($composer, 'extra.merge-plugin', []);

        $defaultMergePlugin = config('extensions.merge_plugin_config', []);
        if (empty($defaultMergePlugin)) {
            $config = require config_path('extensions.php');
            $defaultMergePlugin = $config['merge_plugin_config'];
        }

        if (empty($defaultMergePlugin)) {
            info('Failed to get extensions.merge_plugin_config, please publish the extensions configuration file');

            return;
        }

        $mergePluginConfig = array_merge($defaultMergePlugin, $userMergePluginConfig);

        // merge include
        $diffInclude = array_diff($defaultMergePlugin['include'] ?? [], $userMergePluginConfig['include'] ?? []);
        $mergePluginConfigInclude = array_merge($diffInclude, $userMergePluginConfig['include'] ?? []);

        $mergePluginConfig['include'] = $mergePluginConfigInclude;

        Arr::set($composer, 'extra.merge-plugin', $mergePluginConfig);

        try {
            $content = Json::make()->encode($composer);
            $content .= "\n";

            $fp = fopen($composerPath, 'r+');
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                fwrite($fp, $content);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        } catch (Throwable $e) {
            $message = str_replace(['file_put_contents('.base_path().'/', ')'], '', $e->getMessage());
            throw new RuntimeException('cannot set merge-plugin to '.$message);
        }
    }
}
