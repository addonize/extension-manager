<?php

/*
 * Addonize
 * Copyright (C) 2021-Present Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Addonize\ExtensionManager\Extension;
use Addonize\ExtensionManager\Support\Config\GenerateConfigReader;
use Addonize\ExtensionManager\Support\Process;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NewCommand extends Command
{
    use Traits\StubTrait;

    protected $signature = 'new {fskey}
        {--force}
        ';

    protected $description = 'Create a new laravel package、extension、plugin';

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    protected Extension $extension;

    /**
     * @var string
     */
    protected string $extensionKey;

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle(): mixed
    {
        $this->filesystem = $this->laravel['files'];
        $this->extensionKey = Str::afterLast($this->argument('extKey'), '/');

        $this->extension = new Extension($this->extensionKey);

        // clear directory or exit when plugin exists.
        if (File::exists($this->extension->getExtensionPath())) {
            if (! $this->option('force')) {
                $this->error("Plugin {$this->extension->getExtKey()} exists");

                return Command::FAILURE;
            }

            File::deleteDirectory($this->extension->getExtensionPath());
        }

        $this->generateFolders();
        $this->generateFiles();

        // composer dump-autoload
        Process::run('composer dump-autoload', $this->output);

        $this->info(sprintf("Package [%s] created successfully", $this->extensionKey));

        return Command::SUCCESS;
    }

    /**
     * Get the list of folders will created.
     *
     * @return array
     */
    public function getFolders(): array
    {
        return config('extensions.paths.generator');
    }

    /**
     * Generate the folders.
     */
    public function generateFolders(): void
    {
        foreach ($this->getFolders() as $key => $folder) {
            $folder = GenerateConfigReader::read($key);

            if ($folder->generate() === false) {
                continue;
            }

            $path = config('extensions.paths.extensions').'/'.$this->argument('extKey').'/'.$folder->getPath();

            $this->filesystem->makeDirectory($path, 0755, true);
            if (config('extensions.stubs.gitkeep')) {
                $this->generateGitKeep($path);
            }
        }
    }

    /**
     * Generate git keep to the specified path.
     *
     * @param string $path
     */
    public function generateGitKeep(string $path): void
    {
        $this->filesystem->put($path.'/.gitkeep', '');
    }

    /**
     * Remove git keep from the specified path.
     *
     * @param  string  $path
     */
    public function removeParentDirGitKeep(string $path): void
    {
        if (config('extensions.stubs.gitkeep')) {
            $dirName = dirname($path);
            if (count($this->filesystem->glob("$dirName/*")) >= 1) {
                $this->filesystem->delete("$dirName/.gitkeep");
            }
        }
    }

    /**
     * Get the list of files will created.
     *
     * @return array
     */
    public function getFiles(): array
    {
        return config('extensions.stubs.files');
    }

    /**
     * Generate the files.
     */
    public function generateFiles(): void
    {
        foreach ($this->getFiles() as $stub => $file) {
            $extensionKey = $this->argument('extKey');

            $path = config('extensions.paths.extensions').'/'.$extensionKey.'/'.$file;

            if ($keys = $this->getReplaceKeys($path)) {
                $this->getReplacedContent($file, $keys);
                $path = $this->getReplacedContent($path, $keys);
            }

            $content = $this->getStubContents($stub);

            if ($stub == 'controller.web') {
                if (class_exists(App\Http\Controllers\Controller::class)) {
                    $content = str_replace("use Illuminate\Routing\Controller;", "use App\Http\Controllers\Controller;", $content);
                }
            }

            if (! $this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
                $this->removeParentDirGitKeep($dir);
            }

            $this->filesystem->put($path, $content);
            $this->removeParentDirGitKeep($path);

            $this->info("Created : {$path}");
        }
    }
}
