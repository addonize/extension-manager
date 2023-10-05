<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;

class AddonizeCommand extends Command
{
    protected $signature = 'manage {action?}';

    protected $description = 'Tips export PATH command';

    public function handle(): int
    {
        $vendorBinPath = base_path('vendor/bin');

        $action = $this->argument('action') ?? 'activate';
        if (method_exists($this, $action)) {
            $this->{$action}($vendorBinPath);
        }

        return Command::SUCCESS;
    }

    public function activate(string $vendorBinPath): void
    {
        if (! str_contains(getenv('PATH'), $vendorBinPath)) {
            $this->warn('Add Project vendorBinPath');
            $this->line('');
            $this->warn('Please input this command on your terminal:');
            $this->line(sprintf('export %s', "PATH=$vendorBinPath:".'$PATH'));

            $this->line('');
            $this->warn('Then rerun command to get usage help:');
            $this->line('addonize extension');
        } else {
            $this->warn('Already Add Project vendorBinPath: ');
            $this->line($vendorBinPath);

            $this->line('');
            $this->info('Now you can run command:');
            $this->line('addonize');
        }
    }

    public function deactivate(string $vendorBinPath): void
    {
        $fixErrorPath = str_replace(['::'], '', str_replace($vendorBinPath, '', getenv('PATH')));
        $command = sprintf('export %s', "PATH=$fixErrorPath");

        $this->warn('Remove Project vendorBinPath');
        $this->line('');
        $this->warn('Please input this command on your terminal:');
        $this->line('');
        $this->line($command);
    }
}
