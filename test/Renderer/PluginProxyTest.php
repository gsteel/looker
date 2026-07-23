<?php

declare(strict_types=1);

namespace Looker\Test\Renderer;

use Looker\Renderer\PluginProxy;
use Looker\Renderer\RenderingFailed;
use Looker\Test\InMemoryContainer;
use Looker\Test\Renderer\Plugins\Exceptional;
use Looker\Test\Renderer\Plugins\FluentPlugin;
use Looker\Test\Renderer\Plugins\NotInvokable;
use Looker\Test\Renderer\Plugins\Stateful;
use Looker\Test\Renderer\Plugins\StaticMethod;
use Looker\Test\Renderer\Plugins\WithArguments;
use Looker\Test\Renderer\Plugins\WorkingPlugin;
use PHPUnit\Framework\TestCase;

final class PluginProxyTest extends TestCase
{
    public function testThatAnExceptionIsThrownCallingANonExistentPlugin(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer());

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessageIsOrContains(
            'A plugin with the name "somePlugin" could not be found in the plugin manager',
        );
        $proxy->__call('somePlugin', []);
    }

    public function testThatAnExceptionIsThrownWhenAPluginIsNotInvokable(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new NotInvokable(),
        ]));

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessageIsOrContains(
            'The plugin aliased to "somePlugin" is not callable. Received a type of',
        );
        $proxy->__call('somePlugin', []);
    }

    public function testThatPluginExceptionsAreCaughtAndWrapped(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new Exceptional(),
        ]));

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessageIsOrContains(
            'An exception occurred during execution of the plugin "somePlugin". Message: Oh dear…',
        );
        $proxy->__call('somePlugin', []);
    }

    public function testThatPluginsAreExecutedSuccessfully(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new WorkingPlugin(),
        ]));

        $value = $proxy->__call('somePlugin', []);
        self::assertSame('It Worked!', $value);
    }

    public function testThatPluginsCanExposeThemselves(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new FluentPlugin(),
        ]));

        $somePlugin = $proxy->somePlugin();
        self::assertInstanceOf(FluentPlugin::class, $somePlugin);
        self::assertSame('Bahhh', $somePlugin->getSheep());
    }

    public function testThatPluginsArgumentsArePassedCorrectly(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new WithArguments(),
        ]));

        $value = $proxy->somePlugin('mary', 'had', 'a', 'little', 'lamb');
        self::assertSame('mary had a little lamb', $value);
    }

    public function testThatUsedStatefulPluginsCanBeReset(): void
    {
        $a = new Stateful();
        $b = new Stateful();
        $c = new Stateful();

        $a->add('a');
        $b->add('b');
        $c->add('c');

        self::assertSame('a', (string) $a);
        self::assertSame('b', (string) $b);
        self::assertSame('c', (string) $c);

        $proxy = new PluginProxy(new InMemoryContainer([
            'a' => $a,
            'b' => $b,
            'c' => $c,
        ]));

        $proxy->a()->add('z');
        $proxy->b()->add('z');

        self::assertSame('a, z', (string) $a);
        self::assertSame('b, z', (string) $b);
        self::assertSame('c', (string) $c);

        $proxy->clearPluginState();

        self::assertSame('', (string) $a);
        self::assertSame('', (string) $b);
        self::assertSame('c', (string) $c);
    }

    public function testThatStaticCallablesCanBeUsed(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => StaticMethod::getValue(...),
        ]));

        /** @var mixed $value */
        $value = $proxy->somePlugin();
        self::assertSame('foo', $value);
    }

    public function testHas(): void
    {
        $proxy = new PluginProxy(new InMemoryContainer([
            'somePlugin' => new NotInvokable(),
        ]));

        self::assertTrue($proxy->has('somePlugin'));
        self::assertFalse($proxy->has('foo'));
    }
}
