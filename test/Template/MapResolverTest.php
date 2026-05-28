<?php

declare(strict_types=1);

namespace Looker\Test\Template;

use Looker\Template\MapResolver;
use PHPUnit\Framework\TestCase;

final class MapResolverTest extends TestCase
{
    public function testThatTemplatesCanBeResolved(): void
    {
        $resolver = new MapResolver(['foo' => 'bar']);
        self::assertSame('bar', $resolver->resolve('foo'));
    }

    public function testFalseIsReturnedWhenATemplateCannotBeResolved(): void
    {
        $resolver = new MapResolver(['foo' => 'bar']);

        self::assertFalse($resolver->resolve('fred'));
    }
}
