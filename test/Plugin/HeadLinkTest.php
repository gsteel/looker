<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadLink;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use PHPUnit\Framework\TestCase;

class HeadLinkTest extends TestCase
{
    private HeadLink $plugin;

    protected function setUp(): void
    {
        $this->plugin = new HeadLink(
            Doctype::HTML5,
            new HtmlAttributes(new Escaper()),
            "\n",
        );
    }

    public function testThatASingleLinkWillHaveTheExpectedOutput(): void
    {
        $this->plugin->append('stylesheet', '/assets/styles.css');

        $expect = '<link href="&#x2F;assets&#x2F;styles.css" rel="stylesheet">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatAppendingAndPrependingElementsYieldsTheExpectedOrder(): void
    {
        $this->plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testRemovalWithLink(): void
    {
        $this->plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->removeWithLink('baz');

        $expect = <<<'HTML'
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testRemovalWithLinkIsANoOpWhenThereAreNoMatches(): void
    {
        $this->plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->removeWithLink('not-there');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">
            <link href="foo" rel="stylesheet">
            <link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatDuplicateEntriesWillNotBeOutput(): void
    {
        $this->plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'foo')
            ->append('stylesheet', 'foo');

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatBooleanAttributesAreOmittedWhenFalse(): void
    {
        $this->plugin->append('stylesheet', 'foo', ['disabled' => false]);

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatBooleanAttributesAreIncludedWhenTrue(): void
    {
        $this->plugin->append('stylesheet', 'foo', ['disabled' => true]);

        $expect = '<link disabled href="foo" rel="stylesheet">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatUnknownAttributesAreSkipped(): void
    {
        $this->plugin->append('stylesheet', 'foo', ['unknown' => 'whatever']);

        $expect = '<link href="foo" rel="stylesheet">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatKnownAttributesAreIncluded(): void
    {
        $this->plugin->append('next', 'foo', ['hreflang' => 'en', 'accesskey' => 'v']);

        $expect = '<link accesskey="v" href="foo" hreflang="en" rel="next">';

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatTheSeparatorCanBeConfiguredAtRuntime(): void
    {
        $this->plugin->append('stylesheet', 'foo')
            ->append('stylesheet', 'bar')
            ->prepend('stylesheet', 'baz')
            ->setSeparator(':');

        $expect = <<<'HTML'
            <link href="baz" rel="stylesheet">:<link href="foo" rel="stylesheet">:<link href="bar" rel="stylesheet">
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testResetState(): void
    {
        $this->plugin->append('stylesheet', 'foo')->resetState();

        self::assertSame('', $this->plugin->toString());
    }

    public function testInvokeReturnsSelf(): void
    {
        self::assertSame($this->plugin, $this->plugin->__invoke());
    }

    public function testCanBeCastToString(): void
    {
        $this->plugin->append('stylesheet', 'foo');

        self::assertSame('<link href="foo" rel="stylesheet">', (string) $this->plugin);
    }

    public function testThatClosingSlashWillBeIncludedWhenTheDoctypeIsXhtml(): void
    {
        $plugin = new HeadLink(
            Doctype::XHTML1Strict,
            new HtmlAttributes(new Escaper()),
            "\n",
        );

        $plugin->append('stylesheet', 'foo');

        self::assertSame('<link href="foo" rel="stylesheet" />', $plugin->toString());
    }
}
