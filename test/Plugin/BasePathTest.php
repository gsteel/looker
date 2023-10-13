<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\BasePath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BasePathTest extends TestCase
{
    /** @return list<array{0: non-empty-string, 1: non-empty-string|null, 2: non-empty-string}> */
    public static function dataProvider(): array
    {
        return [
            ['/', '/some-path', '/some-path'],
            ['/foo', '/some-path', '/foo/some-path'],
            ['/foo', 'some-path', '/foo/some-path'],
            ['/foo', 'some-path/bar', '/foo/some-path/bar'],
            ['/foo/', 'some-path', '/foo/some-path'],
            ['/foo/', '/some-path', '/foo/some-path'],
            ['/foo/', null, '/foo/'],
            ['/foo', null, '/foo'],
        ];
    }

    /**
     * @param non-empty-string $basePath
     * @param non-empty-string|null $input
     * @param non-empty-string $expect
     */
    #[DataProvider('dataProvider')]
    public function testExpectedBehaviour(string $basePath, string|null $input, string $expect): void
    {
        $plugin = new BasePath($basePath);

        self::assertSame($expect, $plugin($input));
    }
}
