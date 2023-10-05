<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginActivateCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:activate {extKey?}';

    protected $description = 'Activate Plugin';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();

        if ($extensionKey) {
            $this->activate($extensionKey);
        }
        // Activate all plugins
        else {
            $this->activateAll();
        }

        $this->info('Extension activate successfully');

        return Command::SUCCESS;
    }

    public function activateAll(): void
    {
        $extension = new Extension();

        collect($extension->all())->map(function ($extensionKey) {
            $this->activate($extensionKey);
        });
    }

    public function activate(?string $extensionKey = null): bool
    {
        $extension = new Extension($extensionKey);

        $extKey = $extension->getStudlyName();

        event('extension:activating', [[
            'extKey' => $extKey,
        ]]);

        if ($result = $extension->activate()) {
            $this->info(sprintf('Extension %s activate successfully', $extensionKey));
        } else {
            $this->error(sprintf('Extension %s activate failure', $extensionKey));
        }

        event('extension:activated', [[
            'extKey' => $extKey,
        ]]);

        return $result;
    }
}
