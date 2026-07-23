<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\GlobalAttribute;
use Override;
use PHPUnit\Framework\TestCase;

final class AttributeNormaliserTest extends TestCase
{
    private AttributeNormaliser $normaliser;

    #[Override]
    protected function setUp(): void
    {
        $this->normaliser = new AttributeNormaliser(true);
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
            [],
            $this->normaliser->normalise(['fred' => false], new GlobalAttribute()),
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
            [],
            $normaliser->normalise(['muppets' => 'foo'], new GlobalAttribute()),
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
}
