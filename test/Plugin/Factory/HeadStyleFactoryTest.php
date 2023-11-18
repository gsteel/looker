<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadStyleFactory;
use Looker\Plugin\HeadStyle;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadStyleFactoryTest extends TestCase
{
    private InMemoryContainer $plugins;

    protected function setUp(): void
    {
        $this->plugins = new InMemoryContainer([
            HtmlAttributes::class => new HtmlAttributes(new Escaper()),
        ]);
    }

    public function testFactory(): void
    {
        $plugin = (new HeadStyleFactory())(new InMemoryContainer([
            PluginManager::class => $this->plugins,
        ]));
        self::assertInstanceOf(HeadStyle::class, $plugin);
    }
}
