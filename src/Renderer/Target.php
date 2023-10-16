<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\PluginManager;
use Throwable;

use function array_key_exists;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;

/**
 * phpcs:disable Squiz.NamingConventions.ValidVariableName, PSR2.Classes.PropertyDeclaration.Underscore
 *
 * @internal
 */
final class Target
{
    private bool $__renderLock = false;

    /**
     * @param non-empty-string $__template
     * @param array<non-empty-string, mixed> $__variables
     */
    public function __construct(
        private readonly string $__template,
        private readonly array $__variables,
        private readonly PluginManager $__plugins,
        private readonly bool $__strictVariables = true,
    ) {
    }

    /** @throws RenderingFailed If there are any errors or violations during rendering. */
    public function __invoke(): string
    {
        // This variable locks rendering of _this_ instance and prevents calls to $this->__invoke() from causing
        // infinite loops.
        if ($this->__renderLock) {
            throw RenderingFailed::becauseARenderLoopHasBeenDetected($this->__template);
        }

        try {
            $this->__renderLock = true;
            ob_start();
            /**
             * @psalm-var mixed $include
             * @psalm-suppress UnresolvableInclude
             */
            $include = include $this->__template;
            if ($include === false) {
                throw RenderingFailed::becauseTheTemplateFileCouldNotBeIncluded($this->__template);
            }

            $content = ob_get_clean();
            $this->__renderLock = false;

            return $content;
        } catch (Throwable $error) {
            ob_end_clean();

            if ($error instanceof RenderingFailed) {
                throw $error; // Do not wrap our own exceptions
            }

            throw RenderingFailed::becauseOfAnException($this->__template, $error);
        }
    }

    /**
     * Allows variable retrieval only from the composed array of variables
     *
     * @param non-empty-string $name
     */
    public function __get(string $name): mixed
    {
        if (! array_key_exists($name, $this->__variables) && $this->__strictVariables) {
            throw RenderingFailed::becauseOfAccessToAnUndeclaredVariable($name, $this->__template);
        }

        return $this->__variables[$name] ?? null;
    }

    /**
     * Prevent overloading of member variables
     *
     * @param non-empty-string $name
     *
     * @psalm-suppress UnusedParam $value is part of the signature
     */
    public function __set(string $name, mixed $value): never
    {
        throw RenderingFailed::becauseMemberVariablesCannotBeMutated($name, $this->__template);
    }

    /**
     * Proxies calls to unknown methods to the plugin manager
     *
     * @param non-empty-string $method
     * @param array<non-empty-string, mixed> $args
     *
     * @psalm-suppress PossiblyUnusedMethod Method usage cannot be detected by Psalm
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->__plugins->__call($method, $args);
    }
}
