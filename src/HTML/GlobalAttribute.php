<?php

declare(strict_types=1);

namespace Looker\HTML;

use function array_key_exists;
use function in_array;
use function str_starts_with;
use function strtolower;

/** @internal */
final class GlobalAttribute
{
    private const STRING_ATTRIBUTES = [
        'accesskey',
        'class',
        'contenteditable',
        'enterkeyhint',
        'id',
        'is',
        'itemid',
        'itemprop',
        'itemref',
        'itemtype',
        'lang',
        'nonce',
        'part',
        'popover',
        'role',
        'slot',
        'style',
        'tabindex',
        'title',
    ];

    private const PATTERN_ATTRIBUTES = [
        'aria-',
        'data-',
    ];

    private const ENUMERATED = [
        'autocapitalize' => ['off', 'none', 'on', 'sentences', 'words', 'characters'],
        'dir' => ['ltr', 'rtl', 'auto'],
        'draggable' => ['true', 'false'],
        'hidden' => ['', 'hidden', 'until-found'],
        'inputmode' => ['none', 'text', 'decimal', 'numeric', 'tel', 'search', 'email', 'url'],
        'spellcheck' => ['', 'true', 'false'],
        'translate' => ['yes', 'no'],
        'virtualkeyboardpolicy' => ['auto', 'manual'],
    ];

    private const BOOLEAN = [
        'autofocus',
        'inert',
        'itemscope',
    ];

    /** @param non-empty-string $name */
    public static function exists(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::STRING_ATTRIBUTES)
            || array_key_exists($name, self::ENUMERATED)
            || in_array($name, self::BOOLEAN)
            || self::matchesPatternAttributeName($name);
    }

    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool
    {
        $name = strtolower($name);

        return in_array($name, self::BOOLEAN);
    }

    /** @param non-empty-string $name */
    private static function matchesPatternAttributeName(string $name): bool
    {
        $name = strtolower($name);

        foreach (self::PATTERN_ATTRIBUTES as $prefix) {
            if (! str_starts_with($name, $prefix)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
