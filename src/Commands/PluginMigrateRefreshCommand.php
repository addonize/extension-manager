<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginMigrateRefreshCommand extends Command
{
    use Traits\WorkExtensionkeyTrait;

    protected $signature = 'extension:migrate-refresh {extKey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--seed=}
        {--seeder=}
        {--step=}
        ';

    protected $description = 'Reset and rerun the extension migration';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $extension = new Extension($extensionKey);

        if (!$extension->isValidExtension()) {
            return Command::FAILURE;
        }

        if ($extension->isDeactivate()) {
            return Command::FAILURE;
        }

        try {
            $this->call('migrate:refresh', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $extension->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--step' => $this->option('step'),
            ]);

            if ($this->option('seed')) {
                $this->call('extension:seed', [
                    '--class' => $this->option('seeder'),
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Migrate Refresh: {$extension->getExtKey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrate Refresh {$extension->getExtKey()} fail\n");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
