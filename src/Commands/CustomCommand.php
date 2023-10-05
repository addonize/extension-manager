<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;

class CustomCommand extends Command
{
    protected $signature = 'custom';

    protected $description = 'Customize extensions namespace or others by config/extensions.php';

    public function handle(): int
    {
        $to = config_path('extensions.php');

        if (file_exists($to)) {
            $this->error('config/extensions.php is already existed');

            return Command::FAILURE;
        }

        $from = dirname(__DIR__, 2).'/config/extensions.php';

        copy($from, $to);

        $this->line('<info>Config file copied to </info> <comment>['.$to.']</comment>');

        return Command::SUCCESS;
    }
}
