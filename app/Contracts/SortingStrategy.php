<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface SortingStrategy
{
    public static function supports(string $sortBy): bool;

    public static function apply(Builder $query, string $sortBy): Builder;
}
