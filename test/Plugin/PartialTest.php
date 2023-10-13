<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\Partial;
use Looker\Renderer\PhpRenderer;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class PartialTest extends TestCase
{
    private Partial $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $plugins = new InMemoryContainer([]);
        $this->plugin = new Partial(
            new PhpRenderer(
                new MapResolver([
                    'simple' => __DIR__ . '/Partial/templates/simple-partial.phtml',
                    'nested' => __DIR__ . '/Partial/templates/parent.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
        );
        $plugins->setService('partial', $this->plugin);
    }

    public function testThatAPartialWillBeRenderedAsExpected(): void
    {
        self::assertSame('foo', $this->plugin->__invoke('simple', ['value' => 'foo']));
    }

    public function testThatAPartialCanIncludeAPartial(): void
    {
        self::assertSame('bar.foo', $this->plugin->__invoke('nested', ['value' => 'bar', 'child' => 'foo']));
    }

    /**
     * @TODO Solve partials calling stateful plugins without those plugins being
     *       cleared at the end of each render step
     */
    public function testPartialsWithStatefulPluginCallsDoNotResetPluginState(): void
    {
        self::markTestIncomplete(
            'Currently, calls to stateful plugins will cause a state reset after the partial render.',
        );
    }
}
