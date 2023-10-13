<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\Doctype;
use Looker\Value\Doctype as DoctypeEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_map;

class DoctypeTest extends TestCase
{
    /** @return list<array{0: DoctypeEnum}> */
    public static function doctypeProvider(): array
    {
        return array_map(
            static fn (DoctypeEnum $doctype): array => [$doctype],
            DoctypeEnum::cases(),
        );
    }

    #[DataProvider('doctypeProvider')]
    public function testThatThePluginWillOutputTheValueWhenProvidedWithAnEnumInstance(DoctypeEnum $value): void
    {
        self::assertSame($value->value, (new Doctype(DoctypeEnum::HTML5))->__invoke($value));
    }

    #[DataProvider('doctypeProvider')]
    public function testThatThePluginWillOutputTheValueWhenGivenTheStringKeyOfTheEnum(DoctypeEnum $value): void
    {
        self::assertSame($value->value, (new Doctype(DoctypeEnum::HTML5))->__invoke($value->name));
    }

    #[DataProvider('doctypeProvider')]
    public function testThatThePluginWillOutputTheDefaultValueWhenGivenNoArguments(DoctypeEnum $value): void
    {
        self::assertSame($value->value, (new Doctype($value))->__invoke());
    }

    #[DataProvider('doctypeProvider')]
    public function testThatAnInvalidEnumKeyWillYieldTheDefaultDoctype(DoctypeEnum $value): void
    {
        self::assertSame($value->value, (new Doctype($value))->__invoke('Invalid'));
    }
}
