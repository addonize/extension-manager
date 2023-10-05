<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;

class EnterCommand extends Command
{
    protected $signature = 'enter {extKey}';

    protected $description = 'Go to extension directory';

    public function handle(): int
    {
        $extensionRootPath = config('extensions.paths.extensions');
        if (!$extensionRootPath) {
            $this->error('Extension directory not found');

            return Command::FAILURE;
        }

        $extKey = $this->argument('extKey');

        $extensionPath = "{$extensionRootPath}/{$extKey}";
        if (! file_exists($extensionPath)) {
            $this->error("Plugin directory {$extKey} does not exist");

            return Command::FAILURE;
        }

        if (str_contains(strtolower(PHP_OS_FAMILY), 'win')) {
            $extensionPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $extensionPath);
        }

        if (getenv('PWD') != $extensionPath) {
            $this->warn("Go to the plugin {$extKey} directory");
            $this->line('');
            $this->warn('Please input this command on your terminal:');

            $command = sprintf('cd %s', $extensionPath);
            $this->line($command);
            $this->line('');
        } else {
            $this->info("Currently in the plugin {$extKey} directory");
            $this->line($extensionPath);

            $this->line('');
            $this->info("Now you can run command in your extension: {$extKey}");
            $this->line('manage');
        }

        return Command::SUCCESS;
    }
}
