<?php

declare(strict_types=1);

namespace Looker\Test\Model;

use Looker\Model\ChildModel;
use Looker\Model\Model;
use Looker\Model\TerminalModelCannotBeChild;
use PHPUnit\Framework\TestCase;

use function reset;

class ModelTest extends TestCase
{
    public function testBasicAccessors(): void
    {
        $model = Model::new('bar', ['baz' => 'bat']);

        self::assertSame('bar', $model->template());
        self::assertSame(['baz' => 'bat'], $model->variables());
        self::assertSame([], $model->childModels());
        self::assertFalse($model->isTerminal());
    }

    public function testVariablesCanBeAddedOneAtATime(): void
    {
        $model = Model::new('bar', ['baz' => 'bat']);
        $clone = $model->withVariable('a', 'z');

        self::assertNotSame($model, $clone);
        self::assertNotSame($model->variables(), $clone->variables());

        self::assertSame([
            'baz' => 'bat',
            'a' => 'z',
        ], $clone->variables());
    }

    public function testSingleVariablesOverwritesExistingVariables(): void
    {
        $model = Model::new('bar', ['baz' => 'bat']);
        $clone = $model->withVariable('baz', 'foo');

        self::assertSame(['baz' => 'foo'], $clone->variables());
    }

    public function testVariableReplacement(): void
    {
        $model = Model::new('bar', ['baz' => 'bat']);
        $clone = $model->replaceVariables(['a' => 'b']);

        self::assertNotSame($model, $clone);
        self::assertNotSame($model->variables(), $clone->variables());

        self::assertSame(['a' => 'b'], $clone->variables());
    }

    public function testMergeReplace(): void
    {
        $model = Model::new('bar', [
            'a' => 'b',
            'c' => 'd',
        ]);
        $clone = $model->mergeReplace([
            'a' => 1,
            'd' => 2,
        ]);

        self::assertNotSame($model, $clone);
        self::assertNotSame($model->variables(), $clone->variables());

        self::assertEquals([
            'a' => 1,
            'd' => 2,
            'c' => 'd',
        ], $clone->variables());
    }

    public function testMergeRetain(): void
    {
        $model = Model::new('bar', [
            'a' => 'b',
            'c' => 'd',
        ]);
        $clone = $model->mergeRetain([
            'a' => 1,
            'd' => 2,
        ]);

        self::assertNotSame($model, $clone);
        self::assertNotSame($model->variables(), $clone->variables());

        self::assertEquals([
            'a' => 'b',
            'c' => 'd',
            'd' => 2,
        ], $clone->variables());
    }

    public function testThatChildModelsCanBeAdded(): void
    {
        $a = Model::new('foo', []);
        $b = $a->withChild(Model::new('bar', []), 'someVar');

        self::assertNotSame($a, $b);
        self::assertCount(1, $b->childModels());
    }

    public function testThatATerminalModelCannotBeAChild(): void
    {
        $a = Model::terminal('foo', []);
        $this->expectException(TerminalModelCannotBeChild::class);
        /** @psalm-suppress UnusedMethodCall */
        Model::new('bar', [])->withChild($a, 'baz');
    }

    public function testThatChildModelsCanBeAppended(): void
    {
        $parent = Model::new('bar');
        $child = Model::new('baz');
        $clone = $parent->withChild($child, 'someVar');

        self::assertCount(0, $parent->childModels());
        self::assertCount(1, $clone->childModels());

        $array = $clone->childModels();
        $value = reset($array);
        self::assertInstanceOf(ChildModel::class, $value);

        self::assertSame('someVar', $value->captureTo);
        self::assertSame($child, $value->model);
    }

    public function testThatTheTemplateCanBeChanged(): void
    {
        $model = Model::new('foo', ['baz' => 'bat']);
        $clone = $model->withTemplate('bar');

        self::assertSame('foo', $model->template());
        self::assertSame('bar', $clone->template());

        self::assertSame($model->variables(), $clone->variables());
    }
}
