<?php

/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

namespace Addonize\ExtensionManager\Manager;

use Addonize\ExtensionManager\Support\Json;
use Throwable;

class FileManager
{
    protected mixed $file;

    protected mixed $status = [];

    protected Json $extensionsJson;

    public function __construct()
    {
        $this->file = config('extensions.manager.default.file');

        $this->extensionsJson = Json::make($this->file);

        $this->status = $this->extensionsJson->get('addonize');
    }

    public function all()
    {
        return $this->status;
    }

    public function install(string $plugin): bool
    {
        $this->status[$plugin] = false;

        return $this->write();
    }

    public function uninstall(string $plugin): bool
    {
        unset($this->status[$plugin]);

        return $this->write();
    }

    public function activate(string $plugin): bool
    {
        $this->status[$plugin] = true;

        return $this->write();
    }

    public function deactivate(string $plugin): bool
    {
        $this->status[$plugin] = false;

        return $this->write();
    }

    public function isActivate(string $plugin): bool
    {
        if (array_key_exists($plugin, $this->status)) {
            return $this->status[$plugin] == true;
        }

        return false;
    }

    public function isDeactivate(string $plugin): bool
    {
        return !$this->isActivate($plugin);
    }

    public function write(): bool
    {
        $data = $this->extensionsJson->get();
        $data['plugins'] = $this->status;

        try {
            $content = json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT
            );

            return (bool) file_put_contents($this->file, $content);
        } catch (Throwable $e) {
            info('Failed to update extension status: %s'.$e->getMessage());

            return false;
        }
    }
}
