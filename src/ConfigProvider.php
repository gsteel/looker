<?php

declare(strict_types=1);

namespace Looker;

final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'looker' => [
                'strictVariables' => true,
                'passScopeToChildren' => false,
                'plugins' => $this->pluginDependencies(),
                'templates' => [
                    'paths' => [],
                    'map' => [],
                    'aggregate' => [],
                    'defaultSuffix' => 'phtml',
                ],
            ],
            'dependencies' => [
                'factories' => [
                    Renderer\PhpRenderer::class => Renderer\Factory\PhpRendererFactory::class,
                    Template\AggregateResolver::class => Template\Factory\AggregateResolverFactory::class,
                    Template\DirectoryResolver::class => Template\Factory\DirectoryResolverFactory::class,
                    Template\MapResolver::class => Template\Factory\MapResolverFactory::class,
                ],
                'aliases' => [
                    // By default, the MapResolver will be returned when we ask the container for a template resolver
                    Template\Resolver::class => Template\MapResolver::class,
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function pluginDependencies(): array
    {
        return [
            'factories' => [],
            'aliases' => [],
        ];
    }
}
