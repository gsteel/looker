<?php

declare(strict_types=1);

namespace Looker\Test\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Factory\EscaperFactory;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class EscaperFactoryTest extends TestCase
{
    public function testTheEscaperCanBeRetrievedWithZeroConfig(): void
    {
        $escaper = (new EscaperFactory())->__invoke(new InMemoryContainer());
        self::assertInstanceOf(Escaper::class, $escaper);
        self::assertSame('utf-8', $escaper->getEncoding());
    }

    public function testConfigWillOverrideEncoding(): void
    {
        $escaper = (new EscaperFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => ['encoding' => 'iso-8859-1'],
            ],
        ]));
        self::assertInstanceOf(Escaper::class, $escaper);
        self::assertSame('iso-8859-1', $escaper->getEncoding());
    }
}
