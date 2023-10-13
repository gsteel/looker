<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadTitleFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadTitleFactoryTest extends TestCase
{
    public function testThatZeroConfigIsRequired(): void
    {
        $plugin = (new HeadTitleFactory())(new InMemoryContainer());

        self::assertSame('<title></title>', $plugin->toString());

        $plugin('one')->append('two');

        self::assertSame('<title>one - two</title>', $plugin->toString());
    }

    public function testWithConfig(): void
    {
        $plugin = (new HeadTitleFactory())(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'pluginConfig' => [
                        'headTitle' => [
                            'fallbackTitle' => 'Foo',
                            'separator' => '~',
                        ],
                    ],
                ],
            ],
            Escaper::class => new Escaper(),
        ]));

        self::assertSame('<title>Foo</title>', $plugin->toString());

        $plugin->append('a')->append('b');

        self::assertSame('<title>a~b</title>', $plugin->toString());
    }
}
