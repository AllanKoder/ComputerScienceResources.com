<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasCustomProfilePhoto
{
    /**
     * Get the profile photo URL accessor.
     *
     * @return Attribute
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function () {
            // If profile_photo_path is a valid URL, return it directly
            if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
                return $this->profile_photo_path;
            }

            // Otherwise, generate a default avatar URL
            $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
                return mb_substr($segment, 0, 1);
            })->join(' '));

            return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=e05c00&background=fff7cc';
        });
    }
}
