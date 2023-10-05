<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeScheduleProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:schedule-provider {extKey=ScheduleServiceProvider}';

    protected $description = 'Generate a schedule service provider for specified extension';

    public function handle()
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
        return 'schedule-provider';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace."\Providers";
    }
}
