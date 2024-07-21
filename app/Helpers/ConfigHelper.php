<?php

namespace App\Helpers;

class ConfigHelper
{
    public static function getConfigOptions($name)
    {
        return collect(config($name))->map(function ($value, $key) {
            return ['value' => $key, 'label' => strtolower($value)];
        })->values()->toArray();
    }
}
