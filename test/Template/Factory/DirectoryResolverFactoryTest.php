<?php

declare(strict_types=1);

namespace Looker\Test\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\DirectoryResolver;
use Looker\Template\Factory\DirectoryResolverFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class DirectoryResolverFactoryTest extends TestCase
{
    public function testThatConfigMustExist(): void
    {
        $this->expectException(ConfigurationError::class);
        (new DirectoryResolverFactory())->__invoke(new InMemoryContainer());
    }

    public function testThatTheListOfDirectoriesMustBeSet(): void
    {
        $this->expectException(ConfigurationError::class);
        (new DirectoryResolverFactory())->__invoke(new InMemoryContainer(['config' => []]));
    }

    public function testThatTheDefaultSuffixMustBeSet(): void
    {
        $this->expectException(ConfigurationError::class);
        (new DirectoryResolverFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'templates' => [
                        'paths' => [
                            'foo',
                            'bar',
                        ],
                    ],
                ],
            ],
        ]));
    }

    public function testThatTheDirectoryListMustBeNonEmpty(): void
    {
        $this->expectException(ConfigurationError::class);
        (new DirectoryResolverFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'templates' => [
                        'defaultSuffix' => 'foo',
                        'paths' => [],
                    ],
                ],
            ],
        ]));
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
