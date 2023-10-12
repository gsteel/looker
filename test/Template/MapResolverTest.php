<?php

declare(strict_types=1);

namespace Looker\Test\Template;

use Looker\Template\MapResolver;
use Looker\Template\TemplateCannotBeResolved;
use PHPUnit\Framework\TestCase;

class MapResolverTest extends TestCase
{
    public function testThatTemplatesCanBeResolved(): void
    {
        $resolver = new MapResolver(['foo' => 'bar']);
        self::assertSame('bar', $resolver->resolve('foo'));
    }

    public function testThatAnExceptionIsThrownWhenATemplateCannotBeResolved(): void
    {
        $resolver = new MapResolver(['foo' => 'bar']);

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage('"fred"');

        $resolver->resolve('fred');
    }

    public function testThatExceptionsHaveAReferenceToTheFailingResolver(): void
    {
        $resolver = new MapResolver(['foo' => 'bar']);

        try {
            $resolver->resolve('fred');
            self::fail('An exception was not thrown');
        } catch (TemplateCannotBeResolved $e) {
            self::assertSame($resolver, $e->resolver);
        }
    }
}
