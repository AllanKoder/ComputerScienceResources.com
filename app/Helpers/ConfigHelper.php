<?php

namespace App\Helpers;

class ConfigHelper
{
    public static function getConfigOptions($name)
    {
        return collect(config($name))->map(function ($value, $key) {
            return strtolower($value);
        })->values()->toArray();
    }
}
