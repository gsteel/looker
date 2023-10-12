<?php

declare(strict_types=1);

namespace Looker\Test\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\AggregateResolver;
use Looker\Template\DirectoryResolver;
use Looker\Template\Factory\AggregateResolverFactory;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class AggregateResolverFactoryTest extends TestCase
{
    public function testThatConfigMustExist(): void
    {
        $this->expectException(ConfigurationError::class);
        (new AggregateResolverFactory())->__invoke(new InMemoryContainer());
    }

    public function testThatTheListOfResolversMustBeSet(): void
    {
        $this->expectException(ConfigurationError::class);
        (new AggregateResolverFactory())->__invoke(new InMemoryContainer(['config' => []]));
    }

    public function testThatTheListOfResolversMustReferenceResolverServiceNames(): void
    {
        $this->expectException(ConfigurationError::class);
        (new AggregateResolverFactory())->__invoke(new InMemoryContainer([
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
        ]));
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
