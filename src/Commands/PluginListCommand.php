<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PluginListCommand extends Command
{
    protected $signature = 'extension:list';

    protected $description = 'Get the list of installed plugins';

    public function handle()
    {
        $extensionDir = config('extensions.paths.extensions', 'addons/extensions');

        $extensionDirs = File::glob(sprintf('%s/*', rtrim($extensionDir, '/')));

        $rows = [];
        foreach ($extensionDirs as $extensionDir) {
            if (! is_dir($extensionDir)) {
                continue;
            }

            $extensionKey = basename($extensionDir);

            $extension = new Extension($extensionKey);

            $rows[] = $extension->getExtensionInfo();
        }

        $this->table([
            'Extension Key',
            'Validation',
            'Available',
            'Extension Status',
            'Assets Status',
            'Extension Path',
            'Assets Path',
        ], $rows);

        return Command::SUCCESS;
    }

    public function replaceDir(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
