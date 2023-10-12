<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

final class StaticMethod
{
    public static function getValue(): string
    {
        return 'foo';
    }
}
