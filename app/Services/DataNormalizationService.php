<?php

namespace App\Services;
use App\Utilities\UrlUtilities;

class DataNormalizationService
{
    /**
     * Normalize an array by sorting keys and values for consistent comparison
     */
    public function normalize(array $array): array
    {
        ksort($array);

        // Normalize page_url if present
        if (array_key_exists('page_url', $array) && is_string($array['page_url'])) {
            $array['page_url'] = UrlUtilities::normalize($array['page_url']);
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                sort($value);
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
