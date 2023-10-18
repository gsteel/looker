<?php

declare(strict_types=1);

namespace Looker\HTML;

use function array_key_exists;
use function in_array;
use function strtolower;

final class StyleAttribute
{
    private const STRING = [
        'media',
        'nonce',
        'title',
    ];

    private const ENUMERATED = [
        'blocking' => ['render'],
    ];

    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool
    {
        return GlobalAttribute::isBoolean($name);
    }

    /** @param non-empty-string $name */
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || array_key_exists($name, self::ENUMERATED)
            || GlobalAttribute::exists($name);
    }
}
