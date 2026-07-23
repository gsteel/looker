<?php

declare(strict_types=1);

namespace Looker\HTML;

/** @internal */
interface AttributeInformation
{
    /** @param non-empty-string $name */
    public static function isBoolean(string $name): bool;

    /** @param non-empty-string $name */
    public static function exists(string $name): bool;
}
