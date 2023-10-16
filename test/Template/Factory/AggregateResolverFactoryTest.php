<?php

declare(strict_types=1);

namespace Looker\Test\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\AggregateResolver;
use Looker\Template\DirectoryResolver;
use Looker\Template\Factory\AggregateResolverFactory;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AggregateResolverFactoryTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function erroneousConfig(): array
    {
        return [
            'Missing Config' => [[]],
            'Config not an array' => [
                ['config' => 1],
            ],
            'Resolvers not set' => [
                [
                    'config' => ['looker' => []],
                ],
            ],
            'Resolvers not array' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => ['aggregate' => 1],
                        ],
                    ],
                ],
            ],
            'Resolvers do not map to anything' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'aggregate' => [
                                    'foo',
                                    'bar',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'Resolvers map to non resolver instances' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'aggregate' => ['foo'],
                            ],
                        ],
                    ],
                    'foo' => 'bar',
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('erroneousConfig')]
    public function testErroneousConfig(array $config): void
    {
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage(
            'The aggregate template resolver requires that the `config` array is present in the '
            . 'container, and that an array under the key `looker.templates.aggregate` is a list of strings that '
            . 'can be used to fetch other template resolver instances',
        );
        (new AggregateResolverFactory())->__invoke(new InMemoryContainer($config));
    }

    public function testResolverCanBeRetrieved(): void
    {
        $resolver = (new AggregateResolverFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'templates' => [
                        'aggregate' => [
                            'foo',
                            'bar',
                        ],
                    ],
                ],
            ],
            'foo' => new MapResolver([]),
            'bar' => new DirectoryResolver([__DIR__], 'txt'),
        ]));

        self::assertInstanceOf(AggregateResolver::class, $resolver);
    }
}
