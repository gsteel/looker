<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\AttributeInformation;
use Looker\HTML\AttributeNormaliser;
use Looker\HTML\GlobalAttribute;
use Looker\HTML\LinkAttribute;
use Looker\HTML\MetaAttribute;
use Looker\HTML\ScriptAttribute;
use Looker\HTML\StyleAttribute;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeNormaliserTest extends TestCase
{
    private AttributeNormaliser $normaliser;

    #[Override]
    protected function setUp(): void
    {
        $this->normaliser = new AttributeNormaliser();
    }

    public function testThatAttributeKeysAreLowerCased(): void
    {
        self::assertSame(
            ['accesskey' => 'a'],
            $this->normaliser->normalise(['AccessKey' => 'a'], new GlobalAttribute()),
        );
    }

    public function testBooleanFalseValuesAreOmitted(): void
    {
        self::assertSame(
            [],
            $this->normaliser->normalise(['autofocus' => false], new GlobalAttribute()),
        );
    }

    public function testThatBooleanValuesAreCoercedToTrue(): void
    {
        self::assertSame(
            ['autofocus' => true],
            $this->normaliser->normalise(['autofocus' => 1], new GlobalAttribute()),
        );
    }

    public function testThatUnknownBooleansAreSkippedWhenFalse(): void
    {
        self::assertSame(
            ['accesskey' => 'f', 'id' => 'foo'],
            $this->normaliser->normalise([
                'id' => 'foo',
                'fred' => false,
                'accesskey' => 'f',
            ], new GlobalAttribute()),
        );
    }

    public function testThatUnknownBooleansAreNotSkippedWhenTrue(): void
    {
        self::assertSame(
            ['fred' => true],
            $this->normaliser->normalise(['fred' => true], new GlobalAttribute()),
        );
    }

    public function testInvalidAttributesAreIncludedByDefault(): void
    {
        self::assertSame(
            ['muppets' => 'foo'],
            $this->normaliser->normalise(['muppets' => 'foo'], new GlobalAttribute()),
        );
    }

    public function testInvalidAttributesAreFilteredOutWhenDesired(): void
    {
        $normaliser = new AttributeNormaliser(false);

        self::assertSame(
            ['accesskey' => 'f', 'id' => 'foo'],
            $normaliser->normalise([
                'id' => 'foo',
                'muppets' => 'foo',
                'accesskey' => 'f',
            ], new GlobalAttribute()),
        );
    }

    public function testAttributesAreSortedByKeyAscending(): void
    {
        $attributes = [
            'data-b' => 1,
            'data-a' => 2,
        ];
        $expect = [
            'data-a' => '2',
            'data-b' => '1',
        ];

        self::assertSame(
            $expect,
            $this->normaliser->normalise($attributes, new GlobalAttribute()),
        );
    }

    public function testAttributeKeysAreLowercased(): void
    {
        $attributes = [
            'Data-b' => 1,
            'DATA-a' => 2,
        ];
        $expect = [
            'data-a' => '2',
            'data-b' => '1',
        ];

        self::assertSame(
            $expect,
            $this->normaliser->normalise($attributes, new GlobalAttribute()),
        );
    }

    /**
     * @return list<array{
     *     0: AttributeInformation,
     *     1: array<string, scalar|null>,
     *     2: array<string, scalar|null>,
     * }>
     */
    public static function normaliserCaseSensitivity(): array
    {
        return [
            [new GlobalAttribute(), ['CLASS' => 'foo'], ['class' => 'foo']],
            [new GlobalAttribute(), ['ARIA-FOO' => 'foo'], ['aria-foo' => 'foo']],
            [new LinkAttribute(), ['HREF' => '/foo'], ['href' => '/foo']],
            [new MetaAttribute(), ['Content' => '/foo'], ['content' => '/foo']],
            [new ScriptAttribute(), ['CrossOrigin' => 'anonymous'], ['crossorigin' => 'anonymous']],
            [new ScriptAttribute(), ['Async' => true], ['async' => true]],
            [new StyleAttribute(), ['TITLE' => 'Fred'], ['title' => 'Fred']],
        ];
    }

    /**
     * @param array<string, scalar|null> $input
     * @param array<string, scalar|null> $expect
     */
    #[DataProvider('normaliserCaseSensitivity')]
    public function testCaseSensitivity(AttributeInformation $info, array $input, array $expect): void
    {
        self::assertSame(
            $expect,
            $this->normaliser->normalise($input, $info),
        );
    }
}
