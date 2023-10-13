<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\PartialLoopFactory;
use Looker\Plugin\PartialLoop;
use Looker\Renderer\Renderer;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class PartialLoopFactoryTest extends TestCase
{
    public function testThatThePluginRequiresARenderer(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        (new PartialLoopFactory())(new InMemoryContainer());
    }

    public function testThatThePluginCanBeRetrieved(): void
    {
        $plugin = (new PartialLoopFactory())(new InMemoryContainer([
            Renderer::class => $this->createMock(Renderer::class),
        ]));

        self::assertInstanceOf(PartialLoop::class, $plugin);
    }
}
