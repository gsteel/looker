<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadLink;
use Looker\Value\Doctype;
use PHPUnit\Framework\TestCase;

class HeadLinkTest extends TestCase
{
    public function testThatASingleLinkWillHaveTheExpectedOutput(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");
        $plugin->append('stylesheet', '/assets/styles.css');

        $expect = '<link href="&#x2F;assets&#x2F;styles.css" rel="stylesheet">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatAppendingAndPrependingElementsYieldsTheExpectedOrder(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $plugin->toString());
    }

    public function testRemovalWithLink(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->removeWithLink('baz');

        $expect = <<<'HTML'
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $plugin->toString());
    }

    public function testRemovalWithLinkIsANoOpWhenThereAreNoMatches(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->removeWithLink('not-there');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatDuplicateEntriesWillNotBeOutput(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'foo')
            ->append('stylesheet', 'foo');

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatBooleanAttributesAreOmittedWhenFalse(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo', ['disabled' => false]);

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatBooleanAttributesAreIncludedWhenTrue(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo', ['disabled' => true]);

        $expect = '<link disabled href="foo" rel="stylesheet">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatUnknownAttributesAreSkipped(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo', ['unknown' => 'whatever']);

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatKnownAttributesAreIncluded(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('next', 'foo', ['hreflang' => 'en', 'accesskey' => 'v']);

        $expect = '<link accesskey="v" href="foo" hreflang="en" rel="next">';

        self::assertSame($expect, $plugin->toString());
    }

    public function testThatTheSeparatorCanBeConfiguredAtRuntime(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->setSeparator(':');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">:<link href="foo" rel="stylesheet">:<link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $plugin->toString());
    }

    public function testResetState(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo')->resetState();

        self::assertSame('', $plugin->toString());
    }

    public function testInvokeReturnsSelf(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");
        self::assertSame($plugin, $plugin->__invoke());
    }

    public function testCanBeCastToString(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::HTML5, "\n");

        $plugin->append('stylesheet', 'foo');

        self::assertSame('<link href="foo" rel="stylesheet">', (string) $plugin);
    }

    public function testThatClosingSlashWillBeIncludedWhenTheDoctypeIsXhtml(): void
    {
        $plugin = new HeadLink(new Escaper(), Doctype::XHTML1Strict, "\n");

        $plugin->append('stylesheet', 'foo');

        self::assertSame('<link href="foo" rel="stylesheet" />', $plugin->toString());
    }
}
