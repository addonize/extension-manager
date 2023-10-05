<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Support\Process;
use Illuminate\Console\Command;

class PluginComposerUpdateCommand extends Command
{
    protected $signature = 'extension:composer-update';

    protected $description = 'Update all extensions composer package';

    public function handle()
    {
        $process = Process::run(<<<"SHELL"
            echo "current user:" `whoami`
            echo "home path permission is:" `ls -ld ~`
            echo ""

            #test -f ~/.config/composer/compsoer.json && echo 1 || (mkdir -p ~/.config/composer && echo "{}" > ~/.config/composer/composer.json)
            #echo ""

            echo "global composer.json content": `cat ~/.config/composer/composer.json`
            echo ""

            echo "PATH:" `echo \$PATH`
            echo ""

            echo "php:" `which php` "\n version" `php -v`
            echo "composer:" `which composer` "\n version" `composer --version`
            echo "git:" `which git` "\n version" `git --version`
            echo ""

            # install command
            composer diagnose
            composer update
        SHELL, $this->output);

        if (! $process->isSuccessful()) {
            $this->error('Failed to install packages, calc composer.json hash value fail');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
