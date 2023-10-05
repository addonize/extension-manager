<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;

class PluginMakeCommand extends Command
{
    protected $signature = 'extension:make {extKey}
        {--force}
        ';

    protected $description = 'Alias of new command';

    public function handle()
    {
        return $this->call('new', [
            'extKey' => $this->argument('extKey'),
            '--force' => $this->option('force'),
        ]);
    }
}
