<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadMetaFactory;
use Looker\Plugin\HeadMeta;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadMetaFactoryTest extends TestCase
{
    private InMemoryContainer $plugins;

    protected function setUp(): void
    {
        $this->plugins = new InMemoryContainer([
            HtmlAttributes::class => new HtmlAttributes(new Escaper()),
        ]);
    }

    public function testPluginCanBeRetrievedWhenThePluginManagerIsAvailable(): void
    {
        $plugin = (new HeadMetaFactory())(new InMemoryContainer([
            PluginManager::class => $this->plugins,
        ]));
        self::assertInstanceOf(HeadMeta::class, $plugin);
    }
}
