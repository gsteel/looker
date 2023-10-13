<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\DoctypeFactory;
use Looker\Test\InMemoryContainer;
use Looker\Value\Doctype;
use PHPUnit\Framework\TestCase;

class DoctypeFactoryTest extends TestCase
{
    public function testZeroConfigWillYieldADefaultDoctypeOfHtml5(): void
    {
        $plugin = (new DoctypeFactory())(new InMemoryContainer());

        self::assertSame(Doctype::HTML5->value, $plugin->__invoke());
    }

    public function testThatTheDefaultDoctypeCanBeSetViaConfig(): void
    {
        $plugin = (new DoctypeFactory())(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'pluginConfig' => [
                        'doctype' => Doctype::HTML4Frameset,
                    ],
                ],
            ],
        ]));

        self::assertSame(Doctype::HTML4Frameset->value, $plugin->__invoke());
    }
}
