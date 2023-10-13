<?php

declare(strict_types=1);

namespace Looker\Test\Integration\NestedModel;

use Looker\Model\Model;
use Looker\Renderer\PhpRenderer;
use Looker\Renderer\PluginProxy;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

final class NestedModelTest extends TestCase
{
    private PhpRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new PhpRenderer(
            new MapResolver([
                't1' => __DIR__ . '/templates/level1.phtml',
                't2' => __DIR__ . '/templates/level2.phtml',
                't3' => __DIR__ . '/templates/level3.phtml',
            ]),
            new PluginProxy(new InMemoryContainer()),
            true,
            false,
        );
    }

    public function testExpectedOutput(): void
    {
        $model = Model::new('t1')->withChild(
            Model::new('t2')->withChild(
                Model::new('t3'),
                'level3',
            ),
            'level2',
        );

        $output = $this->renderer->render($model);

        self::assertSame(<<<'HTML'
            <p>Level 1</p>
            <p>Level 2</p>
            <p>Level 3</p>
            
            HTML, $output);
    }
}
