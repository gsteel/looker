<?php

declare(strict_types=1);

namespace Looker\Test\Integration\ParentToChildScope;

use Looker\Model\Model;
use Looker\Renderer\PhpRenderer;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

final class ParentToChildScopeTest extends TestCase
{
    private MapResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new MapResolver([
            'parent' => __DIR__ . '/templates/parent.phtml',
            'child' => __DIR__ . '/templates/child.phtml',
        ]);
    }

    public function testThatParentVariablesAreMergedWithChildVariablesWhenEnabled(): void
    {
        $parentModel = Model::new('parent', [
            'parent1' => 'parent1',
            'parent2' => 'parent2',
        ]);

        $childModel = Model::new('child', [
            'child1' => 'child1',
            'parent2' => 'child2',
        ]);

        $renderer = new PhpRenderer(
            $this->resolver,
            new InMemoryContainer(),
            true,
            true,
        );

        $content = $renderer->render($parentModel->withChild($childModel, 'content'));

        self::assertSame(<<<'HTML'
            <p>parent1</p>
            <p>parent2</p>
            <p>parent1</p>
            <p>child2</p>
            <p>child1</p>
            
            HTML, $content);
    }
}
