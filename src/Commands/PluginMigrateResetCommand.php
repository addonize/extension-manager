<?php

/*
 * Addonize
 * Copyright (C) 2021-Present Ifeoluwa Adewunmi
 * Released under the Apache-2.0 License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginMigrateResetCommand extends Command
{
    use Traits\WorkExtensionkeyTrait;

    protected $signature = 'extension:migrate-reset {extKey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        ';

    protected $description = 'Rollback of all migrations of the extension';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $plugin = new Extension($extensionKey);

        if (! $plugin->isValidExtension()) {
            return Command::FAILURE;
        }

        if ($plugin->isDeactivate()) {
            return Command::FAILURE;
        }

        try {
            $this->call('migrate:reset', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $plugin->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--pretend' => $this->option('pretend') ?? false,
            ]);

            $this->info("Migrate Reset: {$plugin->getExtKey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrate Reset {$plugin->getExtKey()} fail\n");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
