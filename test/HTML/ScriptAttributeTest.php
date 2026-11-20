<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\ScriptAttribute;
use PHPUnit\Framework\TestCase;

final class ScriptAttributeTest extends TestCase
{
    public function testIsBoolean(): void
    {
        self::assertTrue(ScriptAttribute::isBoolean('async'));
        self::assertTrue(ScriptAttribute::isBoolean('ASYNC'));
        self::assertTrue(ScriptAttribute::isBoolean('inert'));
        self::assertFalse(ScriptAttribute::isBoolean('src'));
    }
}
