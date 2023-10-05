<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginMigrateRollbackCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:migrate-rollback {extKey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        {--step=}
        ';

    protected $description = 'Rollback the latest migration of the extension';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();
        $extension = new Extension($extensionKey);

        if (! $extension->isValidExtension()) {
            return Command::FAILURE;
        }

        if (! $extension->isDeactivate()) {
            return Command::FAILURE;
        }

        try {
            $path = $extension->getMigratePath();
            if (glob("$path/*")) {
                $exitCode = $this->call('migrate:reset', [
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                    '--path' => $extension->getMigratePath(),
                    '--realpath' => $this->option('realpath') ?? true,
                    '--pretend' => $this->option('pretend') ?? false,
                ]);

                $this->info("Migrate Rollback: {$extension->getExtKey()}");
                $this->info('Migrate Rollback Path: '.str_replace(base_path().'/', '', $path));

                if ($exitCode != 0) {
                    return $exitCode;
                }
            } else {
                $this->info('Migrate Rollback: Nothing need to rollback');
            }
        } catch (\Throwable $e) {
            $this->warn("Migrate Rollback {$extension->getExtKey()} fail\n");
            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
