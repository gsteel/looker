<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadTitle;
use PHPUnit\Framework\TestCase;

class HeadTitleTest extends TestCase
{
    public function testThatTheOrderOfElementsHasTheExpectedOutcome(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ');

        $title = $plugin->set('had a')
            ->prepend('Mary')
            ->append('little lamb')
            ->setSeparator(' ')
            ->toString();

        self::assertSame('<title>Mary had a little lamb</title>', $title);
    }

    public function testThatSeparatorAndValuesAreEscaped(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' & ');

        $title = $plugin->append('Apples & Oranges')
            ->append('Pears')
            ->toString();

        self::assertSame('<title>Apples &amp; Oranges &amp; Pears</title>', $title);
    }

    public function testThatSetClearsExistingValues(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ');

        $title = $plugin->append('foo')
            ->append('bar')
            ->set('baz')
            ->toString();

        self::assertSame('<title>baz</title>', $title);
    }

    public function testThePluginCanBeCastToAString(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ');

        $plugin->append('foo')
            ->append('bar');

        self::assertSame('<title>foo - bar</title>', (string) $plugin);
    }

    public function testThePluginWillRetainExistingValuesUntilReset(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ');

        $plugin->append('foo')
            ->append('bar')
            ->setSeparator('|');

        self::assertSame('<title>foo|bar</title>', (string) $plugin);
        self::assertSame('<title>foo|bar</title>', (string) $plugin);

        $plugin->resetState();

        self::assertSame('<title></title>', (string) $plugin);
    }

    public function testThatAFallbackTitleCanBeSet(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ', 'Fallback');

        self::assertSame('<title>Fallback</title>', $plugin->toString());
    }

    public function testThatTheFallbackTitleIsNotUsedWhenAnAlternativeIsProvided(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' - ', 'Fallback');

        $plugin->append('Fred');

        self::assertSame('<title>Fred</title>', $plugin->toString());
    }

    public function testThatInvokeReturnsSelf(): void
    {
        $plugin = new HeadTitle(new Escaper());

        self::assertSame($plugin, $plugin->__invoke());
    }

    public function testThatInvokeWillAppendTheTitleIfProvided(): void
    {
        $plugin = new HeadTitle(new Escaper(), ' * ');

        $plugin('Hello');
        $plugin('World');

        self::assertSame('<title>Hello * World</title>', $plugin->toString());
    }
}
