<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testThatTheLayoutIsInitiallyNull(): void
    {
        $plugin = new Layout();

        self::assertNull($plugin->currentLayout());
    }

    public function testThatTheLayoutCanBeSet(): void
    {
        $plugin = new Layout();
        $plugin->__invoke('foo');

        self::assertSame('foo', $plugin->currentLayout());
    }

    public function testThatStateResetClearsTheCurrentLayout(): void
    {
        $plugin = new Layout();
        $plugin->__invoke('foo');
        $plugin->resetState();

        self::assertNull($plugin->currentLayout());
    }
}
