<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Addonize\ExtensionManager\Support\Json;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class PluginInstallCommand extends Command
{
    protected $signature = 'extension:install {path}
        {--seed}
        {--is_dir}
        ';

    protected $description = 'Install the extension from the specified path';

    public function handle()
    {
        try {
            $path = $this->argument('path');

            if ($this->option('is_dir')) {
                $extensionDirectory = $path;

                if (!strpos($extensionDirectory, '/')) {
                    $extensionDirectory = sprintf("addons/extensions/%s", $extensionDirectory);
                }

                if (str_starts_with($extensionDirectory, '/')) {
                    $extensionDirectory = realpath($extensionDirectory);
                } else {
                    $extensionDirectory = realpath(base_path($extensionDirectory));
                }

                $path = $extensionDirectory;
            }

            if (! $path || ! file_exists($path)) {
                $this->error('Failed to unzip, couldn\'t find the plugin path');

                return Command::FAILURE;
            }

            $extensionPath = str_replace(base_path().'/', '', config('extensions.paths.extensions'));
            if (! str_contains($path, $extensionPath)) {
                $exitCode = $this->call('extension:unzip', [
                    'path' => $path,
                ]);

                if ($exitCode != 0) {
                    return $exitCode;
                }

                $extKey = Cache::pull('install:extension_extKey');
            } else {
                $extKey = basename($path);
            }

            if (! $extKey) {
                info('Failed to unzip, couldn\'t get the plugin fskey');

                return Command::FAILURE;
            }

            $extension = new Extension($extKey);
            if (! $extension->isValidExtension()) {
                $this->error('plugin is invalid');

                return Command::FAILURE;
            }

            $extension->manualAddNamespace();

            event('extension:installing', [[
                'extKey' => $extKey,
            ]]);

            $composerJson = Json::make($extension->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            // @see https://getcomposer.org/doc/03-cli.md#process-exit-codes
            if (count($require) || count($requireDev)) {
                $exitCode = $this->call('extension:composer-update');
                if ($exitCode) {
                    $this->error('Failed to update extension dependency');

                    return Command::FAILURE;
                }
            }

            $this->call('extension:deactivate', [
                'extKey' => $extKey,
            ]);

            $this->call('extension:migrate', [
                'extKey' => $extKey,
            ]);

            if ($this->option('seed')) {
                $this->call('extension:seed', [
                    'extKey' => $extKey,
                ]);
            }

            $extension->install();

            $this->call('extension:publish', [
                'extKey' => $extKey,
            ]);

            event('extension:installed', [[
                'extKey' => $extKey,
            ]]);

            $this->info(sprintf("Installed: %s", $extKey));
        } catch (\Throwable $e) {
            info("Install fail: {$e->getMessage()}");
            $this->error("Install fail: {$e->getMessage()}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
