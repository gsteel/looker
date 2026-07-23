<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\HTML\AttributeNormaliser;
use Looker\Plugin\Factory\JavascriptFactory;
use Looker\Plugin\HtmlAttributes;
use Looker\Plugin\Javascript;
use Looker\PluginManager;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

final class JavascriptFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $plugin = (new JavascriptFactory())(new InMemoryContainer([
            PluginManager::class => new InMemoryContainer([
                HtmlAttributes::class => new HtmlAttributes(new Escaper()),
            ]),
            AttributeNormaliser::class => new AttributeNormaliser(true),
        ]));
        self::assertInstanceOf(Javascript::class, $plugin);
    }
}
