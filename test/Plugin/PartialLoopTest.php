<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Looker\Plugin\Partial;
use Looker\Plugin\PartialLoop;
use Looker\Renderer\PhpRenderer;
use Looker\Template\MapResolver;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\TestCase;

class PartialLoopTest extends TestCase
{
    private PartialLoop $plugin;

    protected function setUp(): void
    {
        parent::setUp();

        $plugins = new InMemoryContainer([]);
        $renderer = new PhpRenderer(
            new MapResolver([
                'li' => __DIR__ . '/Partial/templates/list-item.phtml',
                'ul' => __DIR__ . '/Partial/templates/list.phtml',
            ]),
            $plugins,
            true,
            false,
        );

        $this->plugin = new PartialLoop($renderer);
        $partial = new Partial($renderer);
        $plugins->setService('partialLoop', $this->plugin);
        $plugins->setService('partial', $partial);
    }

    public function testThatAPartialWillBeRenderedAsExpected(): void
    {
        $data = [
            ['value' => 'a'],
            ['value' => 'b'],
            ['value' => 'c'],
        ];

        $expect = <<<'HTML'
            <li>a</li>
            <li>b</li>
            <li>c</li>
            
            HTML;

        self::assertSame($expect, $this->plugin->__invoke('li', $data));
    }

    public function testNestedPartialLoop(): void
    {
        $data = [
            [
                'data' => [
                    ['value' => 'a'],
                    ['value' => 'b'],
                    ['value' => 'c'],
                ],
            ],
            [
                'data' => [
                    ['value' => 'z'],
                    ['value' => 'x'],
                ],
            ],
        ];

        $expect = <<<'HTML'
            <ul>
            <li>a</li>
            <li>b</li>
            <li>c</li>
            </ul>
            <ul>
            <li>z</li>
            <li>x</li>
            </ul>
            
            HTML;

        self::assertSame($expect, $this->plugin->__invoke('ul', $data));
    }
}
