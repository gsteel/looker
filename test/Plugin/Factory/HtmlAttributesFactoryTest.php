<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HtmlAttributesFactory;
use Looker\Plugin\HtmlAttributes;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class HtmlAttributesFactoryTest extends TestCase
{
    public function testPluginCanBeRetrievedWithZeroConfig(): void
    {
        $plugin = (new HtmlAttributesFactory())->__invoke(new InMemoryContainer());
        self::assertInstanceOf(HtmlAttributes::class, $plugin);
    }

    public function testPluginCanBeRetrievedWhenTheEscaperIsAlsoAvailable(): void
    {
        $plugin = (new HtmlAttributesFactory())->__invoke(new InMemoryContainer([
            Escaper::class => new Escaper(),
        ]));

        self::assertInstanceOf(HtmlAttributes::class, $plugin);
    }
}
