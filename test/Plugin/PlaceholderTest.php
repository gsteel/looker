<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\Placeholder;
use PHPUnit\Framework\TestCase;

class PlaceholderTest extends TestCase
{
    public function testBasicExpectedBehaviour(): void
    {
        $plugin = new Placeholder();
        $plugin->append('name', 'A')
            ->setSeparator(', ')
            ->append('name', 'B')
            ->prepend('name', 'Z');

        self::assertSame('Z, A, B', $plugin->toString('name'));
    }

    public function testThatCastingToStringWithAnUnusedPlaceholderWillYieldEmptyString(): void
    {
        $plugin = new Placeholder();
        self::assertSame('', $plugin->toString('foo'));
    }

    public function testThatInvokeReturnsSelfWithNoArguments(): void
    {
        $plugin = new Placeholder();
        self::assertSame($plugin, $plugin->__invoke());
    }

    public function testThatInvokeWithPlaceholderNameWillYieldPlaceholderContents(): void
    {
        $plugin = new Placeholder();
        $plugin->append('foo', 'bar');

        self::assertSame('bar', $plugin->__invoke('foo'));
    }

    public function testThatPlaceholdersCanHaveDifferentContentAndDifferentSeparators(): void
    {
        $plugin = new Placeholder();
        $plugin->append('foo', '1')
            ->append('foo', '2')
            ->setSeparator('|', 'foo')
            ->append('bar', 'A')
            ->append('bar', 'B')
            ->setSeparator(',', 'bar');

        self::assertSame('1|2', $plugin->toString('foo'));
        self::assertSame('A,B', $plugin->toString('bar'));
    }

    public function testPlaceholdersCanBeClearedWithoutAffectingOthers(): void
    {
        $plugin = new Placeholder();
        $plugin->append('foo', 'A')
            ->append('bar', 'A')
            ->clear('foo');

        self::assertSame('', $plugin->toString('foo'));
        self::assertSame('A', $plugin->toString('bar'));
    }

    public function testThatSetOverwritesExistingPlaceholderValues(): void
    {
        $plugin = new Placeholder();
        $plugin->append('foo', 'A')
            ->set('foo', 'B');

        self::assertSame('B', $plugin->toString('foo'));
    }

    public function testThatResetStateClearAllPlaceholders(): void
    {
        $plugin = new Placeholder();
        $plugin->append('a', 'b')
            ->append('c', 'd')
            ->resetState();

        self::assertSame('', $plugin->toString('a'));
        self::assertSame('', $plugin->toString('c'));
    }

    public function testThatTheDefaultSeparatorIsAnEmptyString(): void
    {
        $plugin = new Placeholder();
        $plugin->append('a', 'b')
            ->append('a', 'c');

        self::assertSame('bc', $plugin->toString('a'));
    }

    public function testThatTheDefaultSeparatorCanBeChanged(): void
    {
        $plugin = new Placeholder();
        $plugin->append('a', 'b')
            ->append('a', 'c')
            ->append('foo', 'bar')
            ->append('foo', 'baz')
            ->setSeparator(' - ');

        self::assertSame('b - c', $plugin->toString('a'));
        self::assertSame('bar - baz', $plugin->toString('foo'));
    }
}
