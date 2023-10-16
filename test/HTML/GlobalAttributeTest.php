<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\GlobalAttribute;
use PHPUnit\Framework\TestCase;

class GlobalAttributeTest extends TestCase
{
    public function testExists(): void
    {
        self::assertTrue(GlobalAttribute::exists('accesskey'));
        self::assertFalse(GlobalAttribute::exists('foo'));
    }

    public function testExistsIsCaseInsensitive(): void
    {
        self::assertTrue(GlobalAttribute::exists('AccessKey'));
        self::assertFalse(GlobalAttribute::exists('FOO'));
    }

    public function testIsBoolean(): void
    {
        self::assertTrue(GlobalAttribute::isBoolean('autofocus'));
        self::assertFalse(GlobalAttribute::isBoolean('autocapitalize'));
    }

    public function testIsBooleanIsCaseInsensitive(): void
    {
        self::assertTrue(GlobalAttribute::isBoolean('AUTOFOCUS'));
        self::assertFalse(GlobalAttribute::isBoolean('AutoCapitalize'));
    }

    public function testArbitraryAttributesWithPrefixExist(): void
    {
        self::assertTrue(GlobalAttribute::exists('data-nuts'));
        self::assertFalse(GlobalAttribute::exists('roly-poly'));
    }

    public function testPatternMatchingIsCaseInsensitive(): void
    {
        self::assertTrue(GlobalAttribute::exists('DATA-FOO'));
        self::assertTrue(GlobalAttribute::exists('ARIA-Anything'));
        self::assertFalse(GlobalAttribute::exists('Foo-Bar'));
    }
}
