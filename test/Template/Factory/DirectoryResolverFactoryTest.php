<?php

declare(strict_types=1);

namespace Looker\Test\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\DirectoryResolver;
use Looker\Template\Factory\DirectoryResolverFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DirectoryResolverFactoryTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function erroneousConfig(): array
    {
        return [
            'Empty Config' => [[]],
            'Config Not Array' => [
                ['config' => 'foo'],
            ],
            'Empty Directory List' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => [],
                                'defaultSuffix' => 'phtml',
                            ],
                        ],
                    ],
                ],
            ],
            'Directory List Not Array' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => ['paths' => 'foo'],
                            'defaultSuffix' => 'phtml',
                        ],
                    ],
                ],
            ],
            'Empty String in Directory List' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => ['foo', ''],
                                'defaultSuffix' => 'phtml',
                            ],
                        ],
                    ],
                ],
            ],
            'Non String in Directory List' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => [[]],
                                'defaultSuffix' => 'phtml',
                            ],
                        ],
                    ],
                ],
            ],
            'Directory List is a map' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => ['foo' => 'bar'],
                                'defaultSuffix' => 'phtml',
                            ],
                        ],
                    ],
                ],
            ],
            'Default Suffix Not Set' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => ['bar'],
                            ],
                        ],
                    ],
                ],
            ],
            'Default Suffix Empty' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => ['bar'],
                                'defaultSuffix' => '',
                            ],
                        ],
                    ],
                ],
            ],
            'Default Suffix Not String' => [
                [
                    'config' => [
                        'looker' => [
                            'templates' => [
                                'paths' => ['bar'],
                                'defaultSuffix' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('erroneousConfig')]
    public function testRequiredConfiguration(array $config): void
    {
        $this->expectException(ConfigurationError::class);
        $this->expectExceptionMessage(
            'The directory resolver requires that the `config` array is available in the container and '
            . 'that it has a) a list of directory paths under the key `looker.templates.paths` and, b) a non-empty '
            . 'string under the key `looker.templates.defaultSuffix` to use as the default template file name '
            . 'suffix.',
        );

        (new DirectoryResolverFactory())->__invoke(new InMemoryContainer($config));
    }

    public function testResolverCanBeRetrieved(): void
    {
        $resolver = (new DirectoryResolverFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'templates' => [
                        'defaultSuffix' => 'baz',
                        'paths' => [
                            'foo',
                            'bar',
                        ],
                    ],
                ],
            ],
        ]));

        self::assertInstanceOf(DirectoryResolver::class, $resolver);
    }
}
