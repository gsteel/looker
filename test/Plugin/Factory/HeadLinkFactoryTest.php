<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadLinkFactory;
use Looker\Plugin\HeadLink;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadLinkFactoryTest extends TestCase
{
    private InMemoryContainer $plugins;

    protected function setUp(): void
    {
        $this->plugins = new InMemoryContainer([
            HtmlAttributes::class => new HtmlAttributes(new Escaper()),
        ]);
    }

    public function testPluginCanBeRetrievedWithConfiguredAttributeHelper(): void
    {
        $plugin = (new HeadLinkFactory())(new InMemoryContainer([
            PluginManager::class => $this->plugins,
        ]));
        self::assertInstanceOf(HeadLink::class, $plugin);
    }

    public function testPluginCanBeRetrievedWithConfiguredEscaper(): void
    {
        $plugin = (new HeadLinkFactory())(new InMemoryContainer([
            Escaper::class => new Escaper(),
            PluginManager::class => $this->plugins,
        ]));
        self::assertInstanceOf(HeadLink::class, $plugin);
    }
}
