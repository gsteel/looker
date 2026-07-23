<?php

declare(strict_types=1);

namespace Looker\HTML;

use Override;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class LinkAttribute implements AttributeInformation
{
    private const array STRING = [
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

    private const array ENUMERATED = [
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

    private const array BOOLEAN = ['disabled'];

    /** @param non-empty-string $name */
    #[Override]
    public static function isBoolean(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::BOOLEAN, true) || GlobalAttribute::isBoolean($name);
    }

    /** @param non-empty-string $name */
    #[Override]
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return (
            in_array($name, self::STRING, true)
            || in_array($name, self::BOOLEAN, true)
            || array_key_exists($name, self::ENUMERATED)
            || GlobalAttribute::exists($name)
        );
    }
}
