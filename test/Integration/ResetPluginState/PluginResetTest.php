<?php

declare(strict_types=1);

namespace Looker\Test\Integration\ResetPluginState;

use Looker\Model\Model;
use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use Looker\Test\Renderer\Plugins\Stateful;
use Looker\View;
use PHPUnit\Framework\TestCase;

use const PHP_EOL;

final class PluginResetTest extends TestCase
{
    public function testStatefulPluginWorksAsExpected(): void
    {
        $plugin = new Stateful();
        $plugin->add('1')->add('2')->add('3');

        self::assertSame('1, 2, 3', $plugin->__toString());
        self::assertSame('1, 2, 3', $plugin->__toString());
        $plugin->resetState();
        self::assertSame('', $plugin->__toString());
    }

    public function testThatStatefulPluginsAreReset(): void
    {
        $plugin = new Stateful();
        $plugins = new PluginProxy(new InMemoryContainer(['plugin' => $plugin]));
        $renderer = new View(
            new PhpRenderer(
                new MapResolver([
                    'template' => __DIR__ . '/templates/stateful-plugin.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
        );

        $result = $renderer->render(Model::new('template'));

        self::assertSame('1, 2, 3', $result);
        self::assertSame('', $plugin->__toString());
    }

    public function testThatNonStatefulPluginsAreNotReset(): void
    {
        $plugin = new Stateful();
        $plugins = new PluginProxy(new InMemoryContainer([
            'plugin' => $plugin,
            'other' => static function (): string {
                return PHP_EOL . 'Hey!';
            },
        ]));
        $renderer = new View(
            new PhpRenderer(
                new MapResolver([
                    'template' => __DIR__ . '/templates/mixed.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
        );

        $result = $renderer->render(Model::new('template'));

        self::assertSame('1, 2, 3' . PHP_EOL . 'Hey!', $result);
        self::assertSame('', $plugin->__toString());
    }
}
