<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginDeactivateCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:deactivate {extKey?}';

    protected $description = 'Deactivate extension';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();

        if ($extensionKey) {
            $this->deactivate($extensionKey);
        }
        // Deactivate all plugins
        else {
            $this->deactivateAll();
        }

        $this->info('Extension deactivate successfully');

        return Command::SUCCESS;
    }

    public function deactivateAll()
    {
        $plugin = new Plugin();

        collect($plugin->all())->map(function ($extensionKey) {
            $this->deactivate($extensionKey);
        });
    }

    public function deactivate(?string $extensionKey = null): bool
    {
        $plugin = new Extension($extensionKey);
        $extKey = $plugin->getStudlyName();

        event('extension:deactivating', [[
            'extKey' => $extKey,
        ]]);

        if ($result = $plugin->deactivate()) {
            $this->info(sprintf('Extension %s deactivate successfully', $extensionKey));
        } else {
            $this->error(sprintf('Extension %s deactivate failure', $extensionKey));
        }

        event('extension:deactivated', [[
            'extKey' => $extKey,
        ]]);

        return $result;
    }
}
