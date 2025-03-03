<?php

namespace App\Enums;

enum Status: int
{
    case requested = 0;
    case approved = 1;
    case cancelled = 2;

    public static function fromName(string $name): ?int
    {
        foreach (self::cases() as $case) {
            if (strtolower($case->name) === strtolower($name)) {
                return $case->value;
            }
        }
        return null;
    }
}
