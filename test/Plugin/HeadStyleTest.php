<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadStyle;
use PHPUnit\Framework\TestCase;

class HeadStyleTest extends TestCase
{
    private HeadStyle $plugin;

    protected function setUp(): void
    {
        $this->plugin = new HeadStyle(new Escaper(), "\n");
    }

    public function testThatStylesCanBeAppended(): void
    {
        $this->plugin->append('p { color: pink; }')
            ->append('a { color: green; }');

        $expect = <<<'HTML'
            <style>
            p { color: pink; }
            </style>
            <style>
            a { color: green; }
            </style>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testPrependStyle(): void
    {
        $this->plugin->append('p { color: pink; }')
            ->prepend('a { color: green; }');

        $expect = <<<'HTML'
            <style>
            a { color: green; }
            </style>
            <style>
            p { color: pink; }
            </style>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testSeparatorCanBeChanged(): void
    {
        $this->plugin->append('p { color: pink; }')
            ->append('a { color: green; }')
            ->setSeparator('~');

        $expect = <<<'HTML'
            <style>
            p { color: pink; }
            </style>~<style>
            a { color: green; }
            </style>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatAddingTheSameStyleTwiceIsANoOp(): void
    {
        $this->plugin->append('p { color: pink; }')
            ->append('p { color: pink; }');

        $expect = <<<'HTML'
            <style>
            p { color: pink; }
            </style>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testInvokeReturnsSelf(): void
    {
        self::assertSame($this->plugin, $this->plugin->__invoke());
    }

    public function testThatClearingStateRemovesExistingStyles(): void
    {
        $this->plugin->append('p { color: pink; }')
            ->resetState();

        self::assertSame('', $this->plugin->toString());
    }

    public function testThatAttributesCanBeAddedToTheStyleTag(): void
    {
        $this->plugin->append('p { color: pink; }', ['title' => 'Some Style']);

        $expect = <<<'HTML'
            <style title="Some&#x20;Style">
            p { color: pink; }
            </style>
            HTML;

        self::assertSame(
            $expect,
            $this->plugin->toString(),
        );
    }

    public function testThatFalseBooleanAttributesAreOmitted(): void
    {
        $this->plugin->append('p { color: pink; }', ['itemscope' => false, 'title' => 'Foo']);

        $expect = <<<'HTML'
            <style title="Foo">
            p { color: pink; }
            </style>
            HTML;

        self::assertSame(
            $expect,
            $this->plugin->toString(),
        );
    }

    public function testThatBooleanAttributesAreIncludedWithoutValue(): void
    {
        $this->plugin->append('p { color: pink; }', ['itemscope' => true, 'title' => 'Foo']);

        $expect = <<<'HTML'
            <style itemscope title="Foo">
            p { color: pink; }
            </style>
            HTML;

        self::assertSame(
            $expect,
            $this->plugin->toString(),
        );
    }

    public function testThatUnknownAttributesAreOmitted(): void
    {
        $this->plugin->append('p { color: pink; }', ['goats' => 'are great']);

        $expect = <<<'HTML'
            <style>
            p { color: pink; }
            </style>
            HTML;

        self::assertSame(
            $expect,
            $this->plugin->toString(),
        );
    }

    public function testThatCompletelyEmptyStylesAreIgnored(): void
    {
        /** @psalm-suppress InvalidArgument */
        $this->plugin->append('');
        self::assertSame('', $this->plugin->toString());
    }

    public function testThatThePluginCanBeCastToAString(): void
    {
        $this->plugin->append('p { color: pink; }');

        $expect = <<<'HTML'
            <style>
            p { color: pink; }
            </style>
            HTML;

        self::assertSame($expect, (string) $this->plugin);
    }
}
