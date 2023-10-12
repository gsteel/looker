<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Factory;

use Looker\ConfigurationError;
use Looker\PluginManager;
use Looker\Renderer\Factory\PhpRendererFactory;
use Looker\Renderer\PhpRenderer;
use Looker\Template\MapResolver;
use Looker\Template\Resolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class PhpRendererFactoryTest extends TestCase
{
    public function testThatConfigMustBeAvailable(): void
    {
        $this->expectException(ConfigurationError::class);
        (new PhpRendererFactory())->__invoke(new InMemoryContainer());
    }

    public function testThatExpectedConfigVariablesMustHaveTheCorrectType(): void
    {
        $this->expectException(ConfigurationError::class);
        (new PhpRendererFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'strictVariables' => 'Goats',
                    'passScopeToChildren' => 'nah',
                ],
            ],
        ]));
    }

    public function testThatTheResolverMustBeAvailable(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        (new PhpRendererFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'strictVariables' => true,
                    'passScopeToChildren' => false,
                ],
            ],
        ]));
    }

    public function testThatThePluginManagerMustBeAvailable(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        (new PhpRendererFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'strictVariables' => true,
                    'passScopeToChildren' => false,
                ],
            ],
            Resolver::class => new MapResolver([]),
        ]));
    }

    public function testThatTheRendererCanBeRetrieved(): void
    {
        $renderer = (new PhpRendererFactory())->__invoke(new InMemoryContainer([
            'config' => [
                'looker' => [
                    'strictVariables' => true,
                    'passScopeToChildren' => false,
                ],
            ],
            Resolver::class => new MapResolver([]),
            PluginManager::class => new InMemoryContainer(),
        ]));

        self::assertInstanceOf(PhpRenderer::class, $renderer);
    }
}
