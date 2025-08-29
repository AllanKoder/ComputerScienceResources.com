<?php

namespace App\Utilities;

class UrlUtilities
{
    /**
     * Normalize a URL: trim, lowercase, remove trailing slashes.
     */
    public static function normalize(string $url): string
    {
        return rtrim(strtolower(trim($url)), '/');
    }
}
