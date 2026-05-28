<?php

declare(strict_types=1);

namespace Looker\Test\Renderer;

use Looker\Model\Model;
use Looker\PluginManager;
use Looker\Renderer\PhpRenderer;
use Looker\Template\MapResolver;
use Looker\Template\TemplateCannotBeResolved;
use PHPUnit\Framework\TestCase;

final class PhpRendererTest extends TestCase
{
    public function testExceptionThrownWhenTheTemplateCannotBeResolved(): void
    {
        $renderer = new PhpRenderer(
            new MapResolver([]),
            self::createStub(PluginManager::class),
            true,
            false,
        );

        $this->expectException(TemplateCannotBeResolved::class);
        $this->expectExceptionMessage(
            'The template "whatever" cannot be resolved because none of the configured resolvers could find it',
        );

        $renderer->render(Model::new('whatever'));
    }
}
