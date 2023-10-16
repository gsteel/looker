<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadMetaFactory;
use Looker\Plugin\HeadMeta;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HeadMetaFactoryTest extends TestCase
{
    public function testPluginCanBeRetrievedWithZeroConfig(): void
    {
        $plugin = (new HeadMetaFactory())(new InMemoryContainer());
        self::assertInstanceOf(HeadMeta::class, $plugin);
    }

    public function testPluginCanBeRetrievedWithConfiguredEscaper(): void
    {
        $plugin = (new HeadMetaFactory())(new InMemoryContainer([
            Escaper::class => new Escaper(),
        ]));
        self::assertInstanceOf(HeadMeta::class, $plugin);
    }
}
