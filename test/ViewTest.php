<?php

declare(strict_types=1);

namespace Looker\Test;

use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Renderer\RenderingFailed;
use Looker\Template\MapResolver;
use Looker\View;
use Override;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private View $view;

    #[Override]
    protected function setUp(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer());

        $this->view = new View(
            new PhpRenderer(new MapResolver([]), $plugins, true, false),
            $plugins,
        );
    }

    public function testExceptionThrownWhenGivenAnArrayModelButNullTemplate(): void
    {
        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessageIsOrContains(
            'render accepts either a configured view model as its only argument, or, '
            . 'an array of template variables and a template name',
        );
        $this->view->render([], null);
    }
}
