<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginSeedCommand extends Command
{
    use Traits\WorkExtensionkeyTrait;

    protected $signature = 'extension:seed {extKey?}
        {--class=DatabaseSeeder}
        {--database=}
        {--force=}
        ';

    protected $description = 'Run extension migration';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $extension = new Extension($extensionKey);

        if (!$extension->isValidExtension()) {
            return Command::FAILURE;
        }

        try {
            $class = $extension->getSeederNamespace().$this->option('class');

            if (class_exists($class)) {
                $this->call('db:seed', [
                    'class' => $class,
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Seed: {$extension->getExtKey()}");
        } catch (\Throwable $e) {
            $this->warn("Seed {$extension->getExtKey()} fail\n");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
