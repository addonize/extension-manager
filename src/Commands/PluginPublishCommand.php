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

class PluginPublishCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:publish {extKey?}';

    protected $description = 'Distribute static resources of the extension';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $extension = new Extension($extensionKey);

        if ($this->validateExtensionRootPath($extension)) {
            $this->error('Failed to operate extensions root path');

            return Command::FAILURE;
        }

        if (!$extension->isValidExtension()) {
            return Command::FAILURE;
        }

        File::cleanDirectory($extension->getAssetsPath());
        File::copyDirectory($extension->getAssetsSourcePath(), $extension->getAssetsPath());

        $this->info("Published: {$extension->getextKey()}");

        return Command::SUCCESS;
    }
}
