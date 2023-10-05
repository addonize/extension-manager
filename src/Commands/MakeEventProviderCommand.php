<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeEventProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:event-provider {extKey=EventServiceProvider}';

    protected $description = 'Generate an event service provider for specified extension';

    public function handle(): void
    {
        $path = $this->getPath('Providers/'.$this->getNameInput());
        $extensionKey = basename(dirname($path, 3));
        $extensionJsonPath = dirname($path, 3).'/extension.json';

        parent::handle();

        $this->installExtensionProviderAfter(
            $this->getExtensionJsonSearchContent($extensionKey),
            $this->getExtensionJsonReplaceContent($this->getNameInput(), $extensionKey),
            $extensionJsonPath
        );
    }

    protected function getStubName(): string
    {
        return 'event-provider';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace."\Providers";
    }
}
