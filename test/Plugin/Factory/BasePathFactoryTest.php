<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\BasePathFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class BasePathFactoryTest extends TestCase
{
    public function testPluginCanBeRetrievedWithZeroConfig(): void
    {
        $plugin = (new BasePathFactory())(new InMemoryContainer());

        self::assertSame('/foo', $plugin('foo'));
    }

    public function testThatBasePathCanBeSetWithConfig(): void
    {
        $plugin = (new BasePathFactory())(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'pluginConfig' => ['basePath' => '/foo'],
                ],
            ],
        ]));

        self::assertSame('/foo/foo', $plugin('foo'));
    }
}
