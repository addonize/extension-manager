<?php
/*
 * Addonize
 * Copyright (C) 2023 Ifeoluwa Adewunmi
 * Released under the MIT License.
 */

// extension_path
if (! function_exists('extension_path')) {
    // Defines the function 'extension_path'
    function extension_path(string $extKey): string
    {
        return rtrim(config('extensions.paths.extensions'), '/').DIRECTORY_SEPARATOR.$extKey;
    }
}
