<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadLinkFactory;
use Looker\Plugin\HeadLink;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadLinkFactoryTest extends TestCase
{
    public function testPluginCanBeRetrievedWithZeroConfig(): void
    {
        $plugin = (new HeadLinkFactory())(new InMemoryContainer());
        self::assertInstanceOf(HeadLink::class, $plugin);
    }

    public function testPluginCanBeRetrievedWithConfiguredEscaper(): void
    {
        $plugin = (new HeadLinkFactory())(new InMemoryContainer([
            Escaper::class => new Escaper(),
        ]));
        self::assertInstanceOf(HeadLink::class, $plugin);
    }
}
