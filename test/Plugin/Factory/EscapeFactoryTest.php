<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Escape;
use Looker\Plugin\Factory\EscapeFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class EscapeFactoryTest extends TestCase
{
    public function testPluginCanBeRetrievedWithZeroConfig(): void
    {
        $plugin = (new EscapeFactory())(new InMemoryContainer());
        self::assertInstanceOf(Escape::class, $plugin);
    }

    public function testPluginCanBeRetrievedWithConfiguredEscaper(): void
    {
        $escaper = new Escaper();
        $plugin = (new EscapeFactory())(new InMemoryContainer([Escaper::class => $escaper]));

        self::assertSame($escaper, $plugin->escaper);
    }
}
