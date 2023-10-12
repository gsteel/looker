<?php

declare(strict_types=1);

namespace Looker\Test;

use Looker\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /**
     * This test is largely pointless because the config provider has no behaviour, but it's better than Psalm
     * moaning about unused symbols…
     */
    public function testTheConfigProviderWilReturnANonEmptyArray(): void
    {
        self::assertNotSame([], (new ConfigProvider())->__invoke());
    }
}
