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
use Illuminate\Support\Facades\File;

class PluginUninstallCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:uninstall {extKey?}
        {--cleardata=}';

    protected $description = 'Install the extension from the specified path';

    public function handle()
    {
        try {
            $extensionKey = $this->getExtensionKey();
            $extension = new Extension($extensionKey);

            if ($this->validateExtensionRootPath($extension)) {
                $this->error('Failed to operate extensions root path');

                return Command::FAILURE;
            }

            $composerJson = Json::make($extension->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            event('extension:uninstalling', [[
                'extKey' => $extensionKey,
            ]]);

            $this->call('extension:deactivate', [
                'extKey' => $extensionKey,
            ]);

            if ($this->option('cleardata')) {
                $this->call('extension:migrate-rollback', [
                    'extKey' => $extensionKey,
                ]);

                $this->info("Clear Data: {$extensionKey}");
            }

            $this->call('extension:unpublish', [
                'extKey' => $extensionKey,
            ]);

            File::delete($extension->getCachedServicesPath());
            File::deleteDirectory($extension->getExtensionPath());

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            if (count($require) || count($requireDev)) {
                $exitCode = $this->call('extension:composer-update');
                if ($exitCode) {
                    $this->error('Failed to update extension dependency');

                    return Command::FAILURE;
                }
            }

            $extension->uninstall();

            event('extension:uninstalled', [[
                'extKey' => $extensionKey,
            ]]);

            $this->info("Uninstalled: {$extensionKey}");
        } catch (\Throwable $e) {
            info("Uninstall fail: {$e->getMessage()}");
            $this->error("Uninstall fail: {$e->getMessage()}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
