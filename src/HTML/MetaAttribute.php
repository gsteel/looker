<?php

declare(strict_types=1);

namespace Looker\HTML;

use function array_key_exists;
use function in_array;
use function strtolower;

/** @internal */
final class MetaAttribute
{
    private const STRING = [
        'name',
        'content',
        'property',
    ];

    private const ENUMERATED = [
        'charset' => ['utf-8'],
        'http-equiv' => [
            'content-security-policy',
            'content-type',
            'default-style',
            'x-ua-compatible',
            'refresh',
        ],
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
