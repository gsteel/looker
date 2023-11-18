<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\JavascriptFactory;
use Looker\Plugin\HtmlAttributes;
use Looker\Plugin\Javascript;
use Looker\PluginManager;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class JavascriptFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $plugin = (new JavascriptFactory())(new InMemoryContainer([
            PluginManager::class => new InMemoryContainer([
                HtmlAttributes::class => new HtmlAttributes(new Escaper()),
            ]),
        ]));
        self::assertInstanceOf(Javascript::class, $plugin);
    }
}
