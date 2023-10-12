<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Psr\Container\ContainerInterface;
use Throwable;

use function array_keys;
use function is_callable;

/** @internal */
final class PluginProxy
{
    /** @var array<string, null> */
    private array $called = [];

    public function __construct(
        private readonly ContainerInterface $pluginContainer,
    ) {
    }

    /**
     * @param non-empty-string $method
     * @param array<string, mixed> $args
     */
    public function __call(string $method, array $args): mixed
    {
        if (! $this->pluginContainer->has($method)) {
            throw RenderingFailed::becauseAPluginDoesNotExist($method);
        }

        $plugin = $this->pluginContainer->get($method);
        if (! is_callable($plugin)) {
            throw RenderingFailed::becauseAPluginIsNotInvokable($method, $plugin);
        }

        try {
            /** @psalm-var mixed $returnValue */
            $returnValue = $plugin(...$args);
            $this->called[$method] = null;

            return $returnValue;
        } catch (Throwable $e) {
            throw RenderingFailed::becauseOfAPluginException($method, $e);
        }
    }

    /** @return list<string> */
    public function calledPlugins(): array
    {
        return array_keys($this->called);
    }
}
