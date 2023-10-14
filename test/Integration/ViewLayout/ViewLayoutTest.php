<?php

declare(strict_types=1);

namespace Looker\Test\Integration\ViewLayout;

use Looker\Model\Model;
use Looker\Plugin\Layout;
use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Renderer\RenderingFailed;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use Looker\View;
use PHPUnit\Framework\TestCase;

final class ViewLayoutTest extends TestCase
{
    public function testThatTheDefaultLayoutIsRenderedWhenGivenAnArrayAndContentTemplate(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'layout-template' => __DIR__ . '/templates/layout.phtml',
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
            'layout-template',
            'content',
        );

        $content = $view->render(['title' => 'Hello World'], 'content-template');

        $expect = <<<'HTML'
            <header></header>
            <main>
            <h1>Hello World</h1>
            </main>
            <footer></footer>
            
            HTML;

        self::assertSame($expect, $content);
    }

    public function testThatWhenThereIsNoDefaultLayoutAndTheViewDoesNotSpecifyThenNoLayoutWillBeUsed(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
        );

        $content = $view->render(['title' => 'Hello World'], 'content-template');

        $expect = <<<'HTML'
            
            <h1>Hello World</h1>
            
            HTML;

        self::assertSame($expect, $content);
    }

    public function testThatWhenTheViewExplicitlyDisablesTheLayoutThenNoLayoutWillBeUsed(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'layout-template' => __DIR__ . '/templates/layout.phtml',
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
            'layout-template',
            'content',
        );

        $content = $view->render(['title' => 'Hello World', 'layout' => false], 'content-template');

        $expect = <<<'HTML'
            
            <h1>Hello World</h1>
            
            HTML;

        self::assertSame($expect, $content);
    }

    public function testThatWhenTheLayoutPluginIsAvailableAndItSpecifiesALayoutItWillBeUsed(): void
    {
        $plugin = new Layout();
        $plugins = new PluginProxy(new InMemoryContainer([Layout::class => $plugin]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'layout-template' => __DIR__ . '/templates/layout.phtml',
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
            null,
            'content',
        );

        $plugin->__invoke('layout-template');

        $content = $view->render(['title' => 'Hello World', 'layout' => false], 'content-template');

        $expect = <<<'HTML'
            <header></header>
            <main>
            <h1>Hello World</h1>
            </main>
            <footer></footer>
            
            HTML;

        self::assertSame($expect, $content);
    }

    public function testThatWhenTheViewDeclaresAlternativeLayoutThenItWillBeUsed(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'alternative-layout' => __DIR__ . '/templates/alternative-layout.phtml',
                    'layout-template' => __DIR__ . '/templates/layout.phtml',
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
            'layout-template',
            'content',
        );

        $content = $view->render(['title' => 'Hello World', 'layout' => 'alternative-layout'], 'content-template');

        $expect = <<<'HTML'
            <header>My Header</header>
            <main>
            <h1>Hello World</h1>
            </main>

            HTML;

        self::assertSame($expect, $content);
    }

    public function testThatAViewModelIsAcceptableToRender(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([
                    'content-template' => __DIR__ . '/templates/content.phtml',
                ]),
                $plugins,
                true,
                false,
            ),
            $plugins,
        );

        $model = Model::new('content-template', ['title' => 'Hello World']);

        $expect = <<<'HTML'
        
        <h1>Hello World</h1>
        
        HTML;

        self::assertSame($expect, $view->render($model));
    }

    public function testThatAnExceptionWillBeThrownWhenRenderReceivesAnArrayWithoutATemplateName(): void
    {
        $plugins = new PluginProxy(new InMemoryContainer([]));
        $view = new View(
            new PhpRenderer(
                new MapResolver([]),
                $plugins,
                true,
                false,
            ),
            $plugins,
        );

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage('accepts either a configured view model as its only argument, or');

        $view->render(['some' => 'stuff']);
    }
}
