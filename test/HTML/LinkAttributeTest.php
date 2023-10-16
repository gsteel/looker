<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\LinkAttribute;
use PHPUnit\Framework\TestCase;

class LinkAttributeTest extends TestCase
{
    public function testExists(): void
    {
        self::assertTrue(LinkAttribute::exists('rel'));
        self::assertTrue(LinkAttribute::exists('blocking'));
        self::assertTrue(LinkAttribute::exists('disabled'));
        self::assertFalse(LinkAttribute::exists('foo'));
    }

    public function testExistsIsCaseInsensitive(): void
    {
        self::assertTrue(LinkAttribute::exists('REL'));
        self::assertTrue(LinkAttribute::exists('BLOCKING'));
        self::assertTrue(LinkAttribute::exists('DISABLED'));
        self::assertFalse(LinkAttribute::exists('FOO'));
    }

    public function testIsBoolean(): void
    {
        self::assertTrue(LinkAttribute::isBoolean('disabled'));
        self::assertFalse(LinkAttribute::isBoolean('foo'));
    }

    public function testIsBooleanIsCaseInsensitive(): void
    {
        self::assertTrue(LinkAttribute::isBoolean('DISABLED'));
        self::assertFalse(LinkAttribute::isBoolean('FOO'));
    }

    public function testAnyGlobalAttributeExists(): void
    {
        self::assertTrue(LinkAttribute::exists('data-nuts'));
        self::assertTrue(LinkAttribute::exists('ItemScope'));
        self::assertTrue(LinkAttribute::exists('ACCESSKEY'));
    }
}
