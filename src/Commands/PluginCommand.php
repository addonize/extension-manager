<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class PluginCommand extends Command
{
    protected $signature = 'extension';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available commands';

    /**
     * @var string
     */
    public static string $logo = <<<LOGO
    ____  __            _          __  ___
   / __ \/ /_  ______ _(_)___     /  |/  /___ _____  ____ _____ ____  _____
  / /_/ / / / / / __ `/ / __ \   / /|_/ / __ `/ __ \/ __ `/ __ `/ _ \/ ___/
 / ____/ / /_/ / /_/ / / / / /  / /  / / /_/ / / / / /_/ / /_/ /  __/ /
/_/   /_/\__,_/\__, /_/_/ /_/  /_/  /_/\__,_/_/ /_/\__,_/\__, /\___/_/
              /____/                                    /____/
LOGO;

    public function handle(): void
    {
        $this->info(static::$logo);

        $this->comment('');
        $this->comment('Available commands:');

        $this->comment('');
        $this->comment('extension');
        $this->listAdminCommands();
    }

    protected function listAdminCommands(): void
    {
        $commands = collect(Artisan::all())->mapWithKeys(function ($command, $key) {
            if (
                Str::endsWith($key, 'addonize')
                || Str::startsWith($key, 'new')
                || Str::startsWith($key, 'custom')
                || Str::startsWith($key, 'make')
                || Str::startsWith($key, 'extension')
            ) {
                return [$key => $command];
            }

            return [];
        })->toArray();

        \ksort($commands);

        $width = $this->getColumnWidth($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->info(sprintf(" %-{$width}s %s", $command->getName(), $command->getDescription()));
        }
    }

    private function getColumnWidth(array $commands): int
    {
        $widths = [];

        foreach ($commands as $command) {
            $widths[] = static::strlen($command->getName());
            foreach ($command->getAliases() as $alias) {
                $widths[] = static::strlen($alias);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * Returns the length of a string, using mb_strwidth if it is available.
     *
     * @param string $string  The string to check its length
     * @return int The length of the string
     */
    public static function strlen(string $string): int
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }
}
