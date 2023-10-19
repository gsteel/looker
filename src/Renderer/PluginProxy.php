<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\Plugin\StatefulPlugin;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;
use Throwable;

use function array_keys;
use function is_callable;

/** @psalm-internal Looker */
final class PluginProxy implements PluginManager
{
    /** @var array<string, null> */
    private array $called = [];

    public function __construct(
        private readonly ContainerInterface $pluginContainer,
    ) {
    }

    /**
     * @param non-empty-string $id
     *
     * @return callable
     *
     * @throws RenderingFailed If the plugin cannot be found, or, if the plugin is not callable.
     *
     * @psalm-suppress MethodSignatureMismatch - No it's not
     */
    public function get(string $id): mixed
    {
        if (! $this->pluginContainer->has($id)) {
            throw RenderingFailed::becauseAPluginDoesNotExist($id);
        }

        $plugin = $this->pluginContainer->get($id);
        if (! is_callable($plugin)) {
            throw RenderingFailed::becauseAPluginIsNotInvokable($id, $plugin);
        }

        return $plugin;
    }

    public function has(string $id): bool
    {
        return $this->pluginContainer->has($id);
    }

    /**
     * @param non-empty-string $method
     * @param array<string, mixed> $args
     *
     * @throws RenderingFailed If any exceptions occur during plugin retrieval or execution.
     */
    public function __call(string $method, array $args): mixed
    {
        $plugin = $this->get($method);

        try {
            /** @psalm-var mixed $returnValue */
            $returnValue = $plugin(...$args);
            $this->called[$method] = null;

            return $returnValue;
        } catch (Throwable $e) {
            throw RenderingFailed::becauseOfAPluginException($method, $e);
        }
    }

    public function clearPluginState(): void
    {
        foreach (array_keys($this->called) as $name) {
            $plugin = $this->pluginContainer->get($name);
            if (! $plugin instanceof StatefulPlugin) {
                continue;
            }

            $plugin->resetState();
        }

        $this->called = [];
    }
}
