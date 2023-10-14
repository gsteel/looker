<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Looker\Plugin\Factory\PlaceholderFactory;
use Looker\Plugin\Placeholder;
use PHPUnit\Framework\TestCase;

class PlaceholderFactoryTest extends TestCase
{
    public function testThatThePluginCanBeRetrieved(): void
    {
        self::assertInstanceOf(
            Placeholder::class,
            (new PlaceholderFactory())(),
        );
    }
}
