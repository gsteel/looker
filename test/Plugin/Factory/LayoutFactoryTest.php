<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\LayoutFactory;
use Looker\Plugin\Layout;
use PHPUnit\Framework\TestCase;

class LayoutFactoryTest extends TestCase
{
    public function testInvokeReturnsInstance(): void
    {
        self::assertInstanceOf(Layout::class, (new LayoutFactory())());
    }
}
