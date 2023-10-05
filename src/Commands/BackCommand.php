<?php

/*
 * Original Copyright (C) 2021-Present Jevan Tang
 * New Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;

class BackCommand extends Command
{
    protected $signature = 'back';

    protected $description = 'Back to the root directory';

    public function handle(): int
    {
        $basePath = base_path();

        if (str_contains(strtolower(PHP_OS_FAMILY), 'win')) {
            $basePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $basePath);
        }

        if (getenv('PWD') != base_path()) {
            $this->warn('Back to the root directory');
            $this->line('');
            $this->warn('Please input this command on your terminal:');

            $command = sprintf('cd %s', $basePath);
            $this->line($command);
            $this->line('');
        } else {
            $this->info('Currently in the root directory');
            $this->line($basePath);

            $this->line('');
            $this->info('Now you can run command:');
            $this->line('manage or php artisan');
        }

        return Command::SUCCESS;
    }
}
