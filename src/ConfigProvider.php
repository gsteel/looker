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
                'pluginConfig' => [
                    'doctype' => Value\Doctype::HTML5,
                    'headTitle' => [
                        'fallbackTitle' => null, // Add a default head title such as "Untitled"
                        'separator' => ' - ', // Separator for consecutively added title segments: "one - two - three"
                    ],
                    'basePath' => '/', // Provide a base path for the BasePath plugin if you use it
                ],
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
                    // By default, the Concrete PhpRenderer is aliased to the Renderer interface
                    Renderer\Renderer::class => Renderer\PhpRenderer::class,
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function pluginDependencies(): array
    {
        return [
            'factories' => [
                Plugin\BasePath::class => Plugin\Factory\BasePathFactory::class,
                Plugin\Doctype::class => Plugin\Factory\DoctypeFactory::class,
                Plugin\Escape::class => Plugin\Factory\EscapeFactory::class,
                Plugin\HeadLink::class => Plugin\Factory\HeadLinkFactory::class,
                Plugin\HeadMeta::class => Plugin\Factory\HeadMetaFactory::class,
                Plugin\HeadTitle::class => Plugin\Factory\HeadTitleFactory::class,
                Plugin\HtmlAttributes::class => Plugin\Factory\HtmlAttributesFactory::class,
                Plugin\Javascript::class => Plugin\Factory\JavascriptFactory::class,
                Plugin\Layout::class => Plugin\Factory\LayoutFactory::class,
                Plugin\Partial::class => Plugin\Factory\PartialFactory::class,
                Plugin\PartialLoop::class => Plugin\Factory\PartialLoopFactory::class,
                Plugin\Placeholder::class => Plugin\Factory\PlaceholderFactory::class,
                'headScript' => Plugin\Factory\JavascriptFactory::class,
                'inlineScript' => Plugin\Factory\JavascriptFactory::class,
            ],
            'aliases' => [
                'basePath' => Plugin\BasePath::class,
                'doctype' => Plugin\Doctype::class,
                'escape' => Plugin\Escape::class,
                'headLink' => Plugin\HeadLink::class,
                'headMeta' => Plugin\HeadMeta::class,
                'headTitle' => Plugin\HeadTitle::class,
                'htmlAttributes' => Plugin\HtmlAttributes::class,
                'layout' => Plugin\Layout::class,
                'partial' => Plugin\Partial::class,
                'partialLoop' => Plugin\PartialLoop::class,
                'placeholder' => Plugin\Placeholder::class,
            ],
        ];
    }
}
