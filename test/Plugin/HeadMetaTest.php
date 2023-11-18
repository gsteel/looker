<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadMeta;
use Looker\Plugin\HtmlAttributes;
use Looker\Value\Doctype;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use const PHP_EOL;

class HeadMetaTest extends TestCase
{
    private HeadMeta $plugin;

    protected function setUp(): void
    {
        $this->plugin = new HeadMeta(
            Doctype::HTML5,
            new HtmlAttributes(new Escaper()),
            PHP_EOL,
        );
    }

    /** @return array<string, array{0: array<non-empty-string, scalar>, 1: string}> */
    public static function metaDataExamples(): array
    {
        return [
            'charset' => [
                ['charset' => 'utf-8'],
                '<meta charset="utf-8">',
            ],
            'refresh' => [
                [
                    'http-equiv' => 'refresh',
                    'content' => '10;url=https://example.com',
                ],
                '<meta content="10&#x3B;url&#x3D;https&#x3A;&#x2F;&#x2F;example.com" http-equiv="refresh">',
            ],
            'facebook' => [
                [
                    'property' => 'og:type',
                    'content' => 'Making you the product',
                ],
                '<meta content="Making&#x20;you&#x20;the&#x20;product" property="og&#x3A;type">',
            ],
            'viewport' => [
                [
                    'name' => 'viewport',
                    'content' => 'width=device-width, initial-scale=1',
                ],
                '<meta content="width&#x3D;device-width,&#x20;initial-scale&#x3D;1" name="viewport">',
            ],
        ];
    }

    /** @param array<non-empty-string, scalar> $attributes */
    #[DataProvider('metaDataExamples')]
    public function testExpectedOutput(array $attributes, string $expect): void
    {
        $this->plugin->append($attributes);
        self::assertSame($expect, $this->plugin->toString());
    }

    public function testExpectedOrder(): void
    {
        $this->plugin->append(['name' => 'keywords', 'content' => 'foo'])
            ->append(['name' => 'description', 'content' => 'bar'])
            ->prepend(['charset' => 'utf-8']);

        self::assertSame(<<<'HTML'
            <meta charset="utf-8">
            <meta content="foo" name="keywords">
            <meta content="bar" name="description">
            HTML, $this->plugin->toString());
    }

    public function testXHTMLDocumentsWillHaveSelfClosingTag(): void
    {
        $plugin = new HeadMeta(Doctype::XHTML1Strict, new HtmlAttributes(new Escaper()), PHP_EOL);
        $plugin->append(['name' => 'foo', 'content' => 'bar']);
        self::assertSame('<meta content="bar" name="foo" />', $plugin->toString());
    }

    public function testThatEmptyAttributesYieldEmptyString(): void
    {
        $this->plugin->append(['foo' => 'bar']);
        self::assertSame('', $this->plugin->toString());
    }

    public function testThatTheSeparatorCanBeChanged(): void
    {
        $this->plugin->setSeparator('!')
            ->append(['name' => 'foo'])
            ->append(['name' => 'bar']);

        self::assertSame(
            '<meta name="foo">!<meta name="bar">',
            $this->plugin->toString(),
        );
    }

    public function testBooleanAttributesAreSkippedWhenFalse(): void
    {
        $this->plugin->append(['name' => 'foo', 'itemscope' => false]);

        self::assertSame(
            '<meta name="foo">',
            $this->plugin->toString(),
        );
    }

    public function testBooleanAttributesHaveNoValueWhenSet(): void
    {
        $this->plugin->append(['name' => 'foo', 'itemscope' => '']);

        self::assertSame(
            '<meta itemscope name="foo">',
            $this->plugin->toString(),
        );
    }

    public function testInvokeReturnsSelf(): void
    {
        self::assertSame($this->plugin, $this->plugin->__invoke());
    }

    public function testThatThePluginCanBeCastToAString(): void
    {
        $this->plugin->append(['name' => 'foo', 'itemscope' => true]);

        self::assertSame(
            '<meta itemscope name="foo">',
            (string) $this->plugin,
        );
    }

    public function testThatDuplicateTagsAreOmitted(): void
    {
        $this->plugin->append(['name' => 'foo', 'content' => 'bar'])
            ->append(['content' => 'bar', 'name' => 'foo']);

        self::assertSame(
            '<meta content="bar" name="foo">',
            $this->plugin->toString(),
        );
    }

    public function testThatResetStateClearsExistingMeta(): void
    {
        $this->plugin->append(['name' => 'foo', 'content' => 'bar']);
        $this->plugin->resetState();
        self::assertSame('', $this->plugin->toString());
    }

    public function testThatItemsCanBeRemoveByMatchingAnAttributeNameAndValue(): void
    {
        $this->plugin->append(['name' => 'foo', 'content' => 'bar'])
            ->append(['name' => 'foo', 'content' => 'baz'])
            ->append(['name' => 'moo', 'content' => 'cows'])
            ->append(['itemprop' => 'blah', 'content' => 'bat']);

        self::assertSame(<<<'HTML'
            <meta content="bar" name="foo">
            <meta content="baz" name="foo">
            <meta content="cows" name="moo">
            <meta content="bat" itemprop="blah">
            HTML, $this->plugin->toString());

        $this->plugin->removeWithAttributeMatching('name', 'foo');

        self::assertSame(<<<'HTML'
            <meta content="cows" name="moo">
            <meta content="bat" itemprop="blah">
            HTML, $this->plugin->toString());
    }
}
