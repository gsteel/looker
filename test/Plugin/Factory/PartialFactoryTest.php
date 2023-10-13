<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\PartialFactory;
use Looker\Plugin\Partial;
use Looker\Renderer\Renderer;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class PartialFactoryTest extends TestCase
{
    public function testThatThePluginRequiresARenderer(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        (new PartialFactory())(new InMemoryContainer());
    }

    public function testThatThePluginCanBeRetrieved(): void
    {
        $plugin = (new PartialFactory())(new InMemoryContainer([
            Renderer::class => $this->createMock(Renderer::class),
        ]));

        self::assertInstanceOf(Partial::class, $plugin);
    }
}
