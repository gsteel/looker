<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\HeadStyleFactory;
use Looker\Plugin\HeadStyle;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HeadStyleFactoryTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function config(): array
    {
        return [
            'Zero Config' => [[]],
            'Configured Escaper' => [
                [
                    Escaper::class => new Escaper(),
                ],
            ],
        ];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('config')]
    public function testFactory(array $config): void
    {
        $plugin = (new HeadStyleFactory())(new InMemoryContainer($config));
        self::assertInstanceOf(HeadStyle::class, $plugin);
    }
}
