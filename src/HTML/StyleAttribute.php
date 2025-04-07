<?php

declare(strict_types=1);

namespace Looker\HTML;

use Override;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @psalm-internal Looker */
final class StyleAttribute implements AttributeInformation
{
    private const array STRING = [
        'media',
        'nonce',
        'title',
    ];

    private const array ENUMERATED = [
        'blocking' => ['render'],
    ];

    /** @param non-empty-string $name */
    #[Override]
    public static function isBoolean(string $name): bool
    {
        return GlobalAttribute::isBoolean($name);
    }

    /** @param non-empty-string $name */
    #[Override]
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING)
            || array_key_exists($name, self::ENUMERATED)
            || GlobalAttribute::exists($name);
    }
}
