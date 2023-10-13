<?php

declare(strict_types=1);

namespace Looker\HTML;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @internal */
final class LinkAttribute
{
    private const STRING = [
        'as',
        'rel',
        'href',
        'hreflang',
        'imagesizes',
        'imagesrcset',
        'integrity',
        'media',
        'sizes',
        'type',
    ];

    private const ENUMERATED = [
        'blocking' => ['render'],
        'crossorigin' => ['', 'anonymous', 'use-credentials'],
        'fetchpriority' => ['high', 'low', 'auto'],
        'referrerpolicy' => [
            'no-referrer',
            'no-referrer-when-downgrade',
            'origin',
            'origin-when-cross-origin',
            'unsafe-url',
        ],
    ];

    private const BOOLEAN = ['disabled'];

    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::BOOLEAN);
    }

    /** @param non-empty-string $name */
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || in_array($name, self::BOOLEAN)
            || array_key_exists($name, self::ENUMERATED)
            || GlobalAttribute::exists($name);
    }
}
