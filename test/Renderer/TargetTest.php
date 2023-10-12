<?php

declare(strict_types=1);

namespace Looker\Test\Renderer;

use Looker\Renderer\PluginProxy;
use Looker\Renderer\RenderingFailed;
use Looker\Renderer\Target;
use Looker\Test\InMemoryContainer;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Framework\TestCase;

use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_contains;

use const E_WARNING;

class TargetTest extends TestCase
{
    private PluginProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = new PluginProxy(new InMemoryContainer([
            'doStuff' => static function (): string {
                return '<h1>Plugin Output</h1>';
            },
        ]));
    }

    public function testVariablesCanBeRetrievedWhenSet(): void
    {
        $target = new Target('unused', ['value' => 'foo'], $this->proxy, true);

        self::assertSame('foo', $target->value);
    }

    public function testVariablesAreNullWhenUnsetInLaxMode(): void
    {
        $target = new Target('unused', [], $this->proxy, false);

        self::assertNull($target->value);
    }

    public function testAccessingAnUndeclaredVariableWillCauseAnExceptionInStrictMode(): void
    {
        $target = new Target('unused', [], $this->proxy, true);

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage('Access to an undeclared variable "value" in the template "unused"');

        $target->value;
    }

    public function testThatItIsNotPossibleToAccessMemberVariablesOnTheRenderTarget(): void
    {
        $target = new Target('unused', [], $this->proxy, true);
        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage('Access to an undeclared variable "__template" in the template "unused"');
        $target->__template; // phpcs:ignore Squiz.NamingConventions.ValidVariableName
    }

    public function testThatVariablesCannotBeMutatedExternally(): void
    {
        $target = new Target('unused', [], $this->proxy, true);
        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage('Attempt to mutate the variable "value" in the template "unused"');
        $target->value = 'foo';
    }

    public function testVariablesCannotBeMutatedByTemplates(): void
    {
        $template = __DIR__ . '/templates/mutate-value.phtml';
        $target = new Target($template, [], $this->proxy, true);
        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage(
            sprintf('Attempt to mutate the variable "value" in the template "%s"', $template),
        );
        $target->__invoke();
    }

    public function testThatExceptionsThrownWithinTemplatesAreCaughtAndWrapped(): void
    {
        $template = __DIR__ . '/templates/exceptional.phtml';
        $target = new Target($template, [], $this->proxy, true);
        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage(sprintf('An exception occurred during render of "%s"', $template));
        $target->__invoke();
    }

    #[WithoutErrorHandler]
    public function testExceptionIsThrownWhenTheTemplateFileCannotBeIncluded(): void
    {
        // Expect 2 warnings:
        set_error_handler(static function (int $errno, string $errstr): bool {
            self::assertSame(E_WARNING, $errno);
            self::assertTrue(
                str_contains($errstr, 'Failed to open stream') || str_contains($errstr, 'include(): Failed opening'),
            );

            return true;
        }, E_WARNING);

        $template = __DIR__ . '/templates/missing.phtml';
        self::assertFileDoesNotExist($template);
        $target = new Target($template, [], $this->proxy, true);
        try {
            $target->__invoke();
            self::fail('An exception was not thrown');
        } catch (RenderingFailed $error) {
            self::assertStringContainsString(
                'Failed to render template because the template file could not be included',
                $error->getMessage(),
            );
        } finally {
            restore_error_handler();
        }
    }

    public function testThatPluginsAreExecuted(): void
    {
        $template = __DIR__ . '/templates/plugin-output.phtml';
        $target = new Target($template, [], $this->proxy, true);
        $content = $target->__invoke();

        self::assertStringContainsString('<h1>Plugin Output</h1>', $content);
    }

    public function testInvokeCannotBeCalledFromATemplateContext(): void
    {
        $template = __DIR__ . '/templates/invoke.phtml';
        $target = new Target($template, [], $this->proxy, true);

        $this->expectException(RenderingFailed::class);
        $this->expectExceptionMessage('A cyclic rendering dependency has been detected during render of the template');

        $target->__invoke();
    }

    public function testThatVariablesCanHaveInvalidNames(): void
    {
        $template = __DIR__ . '/templates/invalid-variable-names.phtml';
        $target = new Target($template, ['invalid-name' => 'Fred'], $this->proxy, true);

        $content = $target->__invoke();
        self::assertSame('Fred', $content);
    }
}
