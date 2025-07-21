<?php

namespace App\Utils;

class Validator
{
    public static function requireFields(array $data, array $required): array
    {
        $missing = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    public static function isOneOf(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }
}
