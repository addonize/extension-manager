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

class PluginUnpublishCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:unpublish {extKey?}';

    protected $description = 'Distribute static resources of the extension';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $extension = new Extension($extensionKey);

        if (! $extension->isValidExtension()) {
            return Command::FAILURE;
        }

        File::deleteDirectory($extension->getAssetsPath());

        $this->info("Unpublished: {$extension->getExtKey()}");

        return Command::SUCCESS;
    }
}
