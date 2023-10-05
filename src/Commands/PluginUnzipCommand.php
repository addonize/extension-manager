<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Support\Json;
use Addonize\ExtensionManager\Support\Zip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class PluginUnzipCommand extends Command
{
    protected $signature = 'extension:unzip {path}';

    protected $description = 'Unzip the package to the extension directory';

    public function handle()
    {
        $filepath = $this->argument('path');
        try {
            // unzip packaeg and get install command
            $zip = new Zip();
            $tmpDirPath = $zip->unpack($filepath);
        } catch (\Throwable $e) {
            $this->error("Error: file unzip failed, reason: {$e->getMessage()}, filepath is: $filepath");

            return Command::FAILURE;
        }

        if (! is_dir($tmpDirPath)) {
            $this->error(sprintf("install plugin error, plugin unzip dir doesn't exists: %s", $tmpDirPath));

            return Command::FAILURE;
        }

        $extensionJsonPath = sprintf("%s/extension.json", $tmpDirPath);
        if (! file_exists($tmpDirPath)) {
            \info($message = 'Extension file does not exist: '.$extensionJsonPath);
            $this->error('install extension error '.$message);

            return Command::FAILURE;
        }

        $plugin = Json::make($extensionJsonPath);

        $extensionKey = $plugin->get('extKey');
        if (! $extensionKey) {
            \info('Failed to get plugin extKey: '.var_export($extensionKey, true));
            $this->error('install plugin error, plugin.json is invalid plugin json');

            return Command::FAILURE;
        }

        $extensionDir = sprintf('%s/%s',
            config('extensions.paths.extensions'),
            $extensionKey
        );

        if (file_exists($extensionDir)) {
            $this->backup($extensionDir, $extensionKey);
        }

        File::copyDirectory($tmpDirPath, $extensionDir);
        File::deleteDirectory($tmpDirPath);

        Cache::put('install:plugin_extKey', $extensionKey, now()->addMinutes(5));

        return Command::SUCCESS;
    }

    public function backup(string $extensionDir, string $extensionKey): bool
    {
        $backupDir = config('extensions.paths.backups');

        File::ensureDirectoryExists($backupDir);

        if (! is_file($backupDir.'/.gitignore')) {
            file_put_contents($backupDir.'/.gitignore', '*'.PHP_EOL.'!.gitignore');
        }

        $dirs = File::glob("$backupDir/$extensionKey*");

        $currentBackupCount = count($dirs);

        $targetPath = sprintf('%s/%s-%s-%s', $backupDir, $extensionKey, date('YmdHis'), $currentBackupCount + 1);

        File::copyDirectory($extensionDir, $targetPath);
        File::cleanDirectory($extensionDir);

        return true;
    }
}
