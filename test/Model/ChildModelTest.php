<?php

declare(strict_types=1);

namespace Looker\Test\Model;

use Looker\Model\Model;
use Looker\Model\TerminalModelCannotBeChild;
use PHPUnit\Framework\TestCase;

class ChildModelTest extends TestCase
{
    public function testThatTerminalModelsCannotBeChildren(): void
    {
        $terminal = Model::terminal('some-template');
        $model = Model::new('other-template');

        $this->expectException(TerminalModelCannotBeChild::class);

        /** @psalm-suppress UnusedMethodCall */
        $model->withChild($terminal, 'whatever');
    }
}
