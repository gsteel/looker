<?php

declare(strict_types=1);

namespace Looker;

use Laminas;

final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'looker' => [
                'encoding' => 'utf-8',
                'strictVariables' => true,
                'passScopeToChildren' => false,
                'plugins' => $this->pluginDependencies(),
                'pluginConfig' => [],
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
                    Laminas\Escaper\Escaper::class => Plugin\Factory\EscaperFactory::class,
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
            'factories' => [
                Plugin\HtmlAttributes::class => Plugin\Factory\HtmlAttributesFactory::class,
            ],
            'aliases' => [
                'htmlAttributes' => Plugin\HtmlAttributes::class,
            ],
        ];
    }
}
