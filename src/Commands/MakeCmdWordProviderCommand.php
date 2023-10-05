<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace  Addonize\ExtensionManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCmdWordProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:cmdword-provider {extKey=CmdWordServiceProvider}';

    protected $description = 'Generate a cmd word service provider for specified extension';

    public function handle(): void
    {
        $path = $this->getPath('Providers/'.$this->getNameInput());
        $extensionKey = basename(dirname($path, 3));
        $extensionJsonPath = dirname($path, 3).'/extension.json';

        $this->generateCmdWordService($extensionKey);

        parent::handle();

        $this->installExtensionProviderAfter(
            $this->getExtensionJsonSearchContent($extensionKey),
            $this->getExtensionJsonReplaceContent($this->getNameInput(), $extensionKey),
            $extensionJsonPath
        );
    }

    protected function getStubName(): string
    {
        return 'cmd-word-provider';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace."\Providers";
    }

    protected function generateCmdWordService($extensionKey): void
    {
        $getPath = $this->getPath('Services/CmdWordService');
        $dirPath = dirname($getPath);

        if (!is_dir($dirPath)) {
            @mkdir($dirPath, 0755, true);
        }

        if (!is_file($getPath)) {
            $stubPath = __DIR__.'/stubs/cmd-word-service.stub';

            $content = file_get_contents($stubPath);

            $newContent = str_replace('$STUDLY_NAME$', $extensionKey, $content);

            file_put_contents($path, $newContent);
        }
    }
}
