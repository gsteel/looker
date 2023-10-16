<?php

declare(strict_types=1);

namespace Looker\Test\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\Factory\MapResolverFactory;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MapResolverFactoryTest extends TestCase
{
    public function testThatConfigMustExist(): void
    {
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage(
            'The map resolver requires that `config` is an array available in the container and contains '
            . 'an array under the key `looker.templates.map` where all the keys and values are non-empty strings',
        );
        (new MapResolverFactory())->__invoke(new InMemoryContainer());
    }

    public function testThatTheMapMustBeSet(): void
    {
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage(
            'The map resolver requires that `config` is an array available in the container and contains '
            . 'an array under the key `looker.templates.map` where all the keys and values are non-empty strings',
        );
        (new MapResolverFactory())->__invoke(new InMemoryContainer(['config' => []]));
    }

    /** @return list<array{0: array<string, mixed>}> */
    public static function invalidMap(): array
    {
        return [
            [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'map' => [1 => 'foo'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'map' => ['foo' => ''],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $invalidConfig */
    #[DataProvider('invalidMap')]
    public function testThatTheMapMustContainNonEmptyStrings(array $invalidConfig): void
    {
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage(
            'The map resolver requires that `config` is an array available in the container and contains '
            . 'an array under the key `looker.templates.map` where all the keys and values are non-empty strings',
        );
        (new MapResolverFactory())->__invoke(new InMemoryContainer($invalidConfig));
    }

    public function testResolverCanBeRetrieved(): void
    {
        $resolver = (new MapResolverFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'templates' => [
                        'map' => ['foo' => 'bar'],
                    ],
                ],
            ],
        ]));

        self::assertInstanceOf(MapResolver::class, $resolver);
    }
}
