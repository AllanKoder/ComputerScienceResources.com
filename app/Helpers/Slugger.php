<?php

namespace App\Helpers;

class Slugger
{
    public static function make(string $name): string
    {
        return mb_strtolower($name);
    }
}
