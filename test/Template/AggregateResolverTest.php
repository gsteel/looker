<?php

declare(strict_types=1);

namespace Looker\Test\Template;

use Looker\Template\AggregateResolver;
use Looker\Template\MapResolver;
use Looker\Template\TemplateCannotBeResolved;
use PHPUnit\Framework\TestCase;

class AggregateResolverTest extends TestCase
{
    public function testResolutionIsFirstInFirstOut(): void
    {
        $map1 = new MapResolver([
            'tpl' => __DIR__ . '/templates/more/templates.phtml',
        ]);

        $map2 = new MapResolver([
            'tpl' => __DIR__ . '/templates/more/other.txt',
        ]);

        $resolver = new AggregateResolver($map1, $map2);

        self::assertSame(
            __DIR__ . '/templates/more/templates.phtml',
            $resolver->resolve('tpl'),
        );
    }

    public function testThatExceptionsWillNotBeThrownWhenAtLeastOneResolverCanResolve(): void
    {
        $map1 = new MapResolver([
            'foo' => __DIR__ . '/templates/more/templates.phtml',
        ]);

        $map2 = new MapResolver([
            'bar' => __DIR__ . '/templates/more/other.txt',
        ]);

        $resolver = new AggregateResolver($map1, $map2);

        self::assertSame(
            __DIR__ . '/templates/more/other.txt',
            $resolver->resolve('bar'),
        );
    }

    public function testExceptionThrownWhenNoResolversCanResolve(): void
    {
        $map1 = new MapResolver([
            'foo' => __DIR__ . '/templates/more/templates.phtml',
        ]);

        $map2 = new MapResolver([
            'bar' => __DIR__ . '/templates/more/other.txt',
        ]);

        $resolver = new AggregateResolver($map1, $map2);

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage('because none of the configured resolvers could find it');

        $resolver->resolve('baz');
    }
}
