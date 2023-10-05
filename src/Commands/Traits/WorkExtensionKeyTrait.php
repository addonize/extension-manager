<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Commands\Traits;

use Addonize\ExtensionManager\Extension;

/**
 * @method argument(string $string)
 */
trait WorkExtensionKeyTrait
{
    public function getExtensionKey(): string
    {
        $extensionKey = $this->argument('extKey');
        if (!$extensionKey) {
            $extensionRootPath = config('extensions.paths.extensions');
            if (str_contains(getcwd(), $extensionRootPath)) {
                $extensionKey = basename(getcwd());
            }
        }

        return $extensionKey;
    }

    public function validateExtensionRootPath(Extension $extension): bool
    {
        $extensionRootPath = config('extensions.paths.extensions');
        $currentPluginRootPath = rtrim($extension->getExtensionPath(), '/');

        return $extensionRootPath == $currentPluginRootPath;
    }
}
