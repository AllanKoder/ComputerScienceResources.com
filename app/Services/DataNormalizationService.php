<?php

namespace App\Services;

class DataNormalizationService
{
    /**
     * Normalize an array by sorting keys and values for consistent comparison
     */
    public function normalize(array $array): array
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                sort($value); // Assumes it's a flat array of values
            }
        }

        return $array;
    }

    /**
     * Compare two arrays after normalizing them
     */
    public function arraysAreEqual(array $array1, array $array2): bool
    {
        return $this->normalize($array1) === $this->normalize($array2);
    }
}
