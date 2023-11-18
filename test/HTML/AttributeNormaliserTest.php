<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\GlobalAttribute;
use PHPUnit\Framework\TestCase;

class AttributeNormaliserTest extends TestCase
{
    public function testThatAttributeKeysAreLowerCased(): void
    {
        self::assertSame(
            ['accesskey' => 'a'],
            AttributeNormaliser::normalise(['AccessKey' => 'a'], new GlobalAttribute()),
        );
    }

    public function testBooleanFalseValuesAreOmitted(): void
    {
        self::assertSame(
            [],
            AttributeNormaliser::normalise(['autofocus' => false], new GlobalAttribute()),
        );
    }

    public function testThatBooleanValuesAreCoercedToTrue(): void
    {
        self::assertSame(
            ['autofocus' => true],
            AttributeNormaliser::normalise(['autofocus' => 1], new GlobalAttribute()),
        );
    }

    public function testInvalidAttributesAreOmitted(): void
    {
        self::assertSame(
            [],
            AttributeNormaliser::normalise(['muppets' => 'foo'], new GlobalAttribute()),
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
            AttributeNormaliser::normalise($attributes, new GlobalAttribute()),
        );
    }
}
