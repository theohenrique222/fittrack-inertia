<?php

namespace App\Casts;

use App\Enums\Gender;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class NullableGender implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Gender
    {
        if (empty($value)) {
            return null;
        }

        return Gender::from($value);
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Gender) {
            return $value->value;
        }

        return $value;
    }
}
