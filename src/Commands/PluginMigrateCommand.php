<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Illuminate\Console\Command;

class PluginMigrateCommand extends Command
{
    use Traits\WorkExtensionKeyTrait;

    protected $signature = 'extension:migrate {extKey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--schema-path=}
        {--seed=}
        {--seeder=}
        {--step=}
        {--pretend=}
        ';

    protected $description = 'Run extension migration';

    public function handle()
    {
        $extensionKey = $this->getExtensionKey();

        if ($extensionKey) {
            return $this->migrate($extensionKey);
        } else {
            $extension = new Extension();

            collect($extension->all())->map(function ($extensionKey) {
                $this->migrate($extensionKey, true);
            });
        }

        return Command::SUCCESS;
    }

    public function migrate(string $extensionKey, $isAll = false)
    {
        $extension = new Extension($extensionKey);

        if (! $extension->isValidExtension()) {
            return Command::FAILURE;
        }

        if ($extension->isDeactivate() && $isAll) {
            return Command::FAILURE;
        }

        try {
            $this->call('migrate', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $extension->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--schema-path' => $this->option('schema-path'),
                '--pretend' => $this->option('pretend') ?? false,
                '--step' => $this->option('step') ?? false,
            ]);

            if ($this->option('seed')) {
                $this->call('extension:seed', [
                    '--class' => $this->option('seeder'),
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Migrated: {$extension->getExtKey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrated {$extension->getExtKey()} fail\n");
            $this->error($e->getMessage());
        }
    }
}
