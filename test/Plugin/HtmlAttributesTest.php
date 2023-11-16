<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use InvalidArgumentException;
use Laminas\Escaper\Escaper;
use Looker\Plugin\HtmlAttributes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function json_encode;

class HtmlAttributesTest extends TestCase
{
    /** @return list<array{0: array<string, mixed>, 1: string}> */
    public static function attributeProvider(): array
    {
        return [
            [['data-foo' => '1&2'], 'data-foo="1&amp;2"'],
            [
                ['onsubmit' => 'alert("Do some snazzy jabbascript");'],
                'onsubmit="alert&#x28;&quot;Do&#x20;some&#x20;snazzy&#x20;jabbascript&quot;&#x29;&#x3B;"',
            ],
            [['class' => ['a', 'b', 'c']], 'class="a&#x20;b&#x20;c"'],
            [
                ['style' => 'color: red; background: #fff;'],
                'style="color&#x3A;&#x20;red&#x3B;&#x20;background&#x3A;&#x20;&#x23;fff&#x3B;"',
            ],
            [
                ['data-json' => json_encode(['foo' => 'bar'])],
                'data-json="&#x7B;&quot;foo&quot;&#x3A;&quot;bar&quot;&#x7D;"',
            ],
        ];
    }

    /** @param array<string, mixed> $attributes */
    #[DataProvider('attributeProvider')]
    public function testAttributeSerialisation(array $attributes, string $expect): void
    {
        $plugin = new HtmlAttributes(new Escaper());
        self::assertSame(
            $expect,
            $plugin->__invoke($attributes),
        );
    }

    public function testThatNestedArraysWillCauseAnException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'HTML attribute arrays can only contain arrays with scalar values. The attribute "something" is invalid',
        );
        (new HtmlAttributes(new Escaper()))->__invoke([
            'something' => ['not-scalar' => ['Oh noes']],
        ]);
    }

    public function testThatBooleanTrueAttributeValuesWillOnlyContainTheKey(): void
    {
        $plugin = new HtmlAttributes(new Escaper());
        self::assertSame('disabled monkeys="1"', $plugin->__invoke(['disabled' => true, 'monkeys' => 1]));
    }

    public function testThatBooleanFalseAttributesWillBeOmitted(): void
    {
        $plugin = new HtmlAttributes(new Escaper());
        self::assertSame('monkeys="1"', $plugin->__invoke(['disabled' => false, 'monkeys' => 1]));
    }

    public function testThatAttributeValuesCanBeNull(): void
    {
        $plugin = new HtmlAttributes(new Escaper());
        self::assertSame('foo="bar" baz=""', $plugin->__invoke(['foo' => 'bar', 'baz' => null]));
    }
}
