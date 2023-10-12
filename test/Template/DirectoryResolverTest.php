<?php

declare(strict_types=1);

namespace Looker\Test\Template;

use Looker\Template\DirectoryResolver;
use Looker\Template\TemplateCannotBeResolved;
use PHPUnit\Framework\TestCase;

class DirectoryResolverTest extends TestCase
{
    public function testExceptionThrownWhenNoTemplatesCanBeFoundInAnyConfiguredDirectories(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
            __DIR__ . '/templates/and-more/',
        ], 'phtml');

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage(
            'The template "does-not-exist" cannot be resolved to a file on the local filesystem',
        );

        $resolver->resolve('does-not-exist');
    }

    public function testExceptionThrownWhenAConfiguredDirectoryIsNotADirectory(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
            __DIR__ . '/templates/not-directory',
        ], 'phtml');

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage(
            'The path provided for template resolution is not a directory',
        );

        $resolver->resolve('not-relevant');
    }

    public function testThatTheExtensionIsAddedToTheNameArgumentWhenOmitted(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
        ], 'phtml');

        $path = $resolver->resolve('templates');
        self::assertSame(
            __DIR__ . '/templates/more/templates.phtml',
            $path,
        );
    }

    public function testThatTheExtensionIsNotAddedToTheNameArgumentWhenPresent(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
        ], 'phtml');

        $path = $resolver->resolve('templates.phtml');
        self::assertSame(
            __DIR__ . '/templates/more/templates.phtml',
            $path,
        );
    }

    public function testThatAlternativeExtensionsCanBeResolved(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
        ], 'phtml');

        $path = $resolver->resolve('other.txt');
        self::assertSame(
            __DIR__ . '/templates/more/other.txt',
            $path,
        );
    }

    public function testThatResolutionIsFirstInFirstOut(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
            __DIR__ . '/templates/and-more',
        ], 'phtml');

        $path = $resolver->resolve('templates');
        self::assertSame(
            __DIR__ . '/templates/more/templates.phtml',
            $path,
        );

        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/and-more',
            __DIR__ . '/templates/more',
        ], 'phtml');

        $path = $resolver->resolve('templates');
        self::assertSame(
            __DIR__ . '/templates/and-more/templates.phtml',
            $path,
        );
    }

    public function testThatSubdirectoriesCanBeUsedInTemplateNames(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates',
        ], 'phtml');

        $path = $resolver->resolve('more/templates');
        self::assertSame(
            __DIR__ . '/templates/more/templates.phtml',
            $path,
        );
    }

    public function testThatItIsNotPossibleToTraverseUpwardsThroughTheDirectoryTree(): void
    {
        $resolver = new DirectoryResolver([
            __DIR__ . '/templates/more',
        ], 'phtml');

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage(
            'cannot be resolved because it includes upward directory traversal',
        );

        $resolver->resolve('../top.phtml');
    }
}
