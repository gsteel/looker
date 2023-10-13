<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadTitle;
use Looker\Plugin\Partial;
use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class PartialTest extends TestCase
{
    private Partial $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new InMemoryContainer([]);
        $plugins = new PluginProxy($container);
        $this->plugin = new Partial(
            new PhpRenderer(
                new MapResolver([
                    'simple' => __DIR__ . '/Partial/templates/simple-partial.phtml',
                    'nested' => __DIR__ . '/Partial/templates/parent.phtml',
                    'layout' => __DIR__ . '/Partial/templates/layout.phtml',
                    'page-title' => __DIR__ . '/Partial/templates/page-title.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
        );
        $container->setService('partial', $this->plugin);
        $container->setService('headTitle', new HeadTitle(new Escaper()));
    }

    public function testThatAPartialWillBeRenderedAsExpected(): void
    {
        self::assertSame('foo', $this->plugin->__invoke('simple', ['value' => 'foo']));
    }

    public function testThatAPartialCanIncludeAPartial(): void
    {
        self::assertSame('bar.foo', $this->plugin->__invoke('nested', ['value' => 'bar', 'child' => 'foo']));
    }

    public function testPartialsWithStatefulPluginCallsDoNotResetPluginState(): void
    {
        $expect = <<<'HTML'
            <title>Partial - Layout</title>
            <p>Partial Content</p>
            
            HTML;

        self::assertSame($expect, $this->plugin->__invoke('layout'));
    }
}
