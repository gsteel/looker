<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\Plugin\StatefulPlugin;
use Looker\PluginManager;
use Override;
use Psr\Container\ContainerInterface;
use Throwable;

use function array_keys;
use function is_callable;

/** @psalm-no-seal-methods Any "Magic" method might be called on this object to execute 'plugins' */
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
     * @mago-expect analysis:incompatible-parameter-type Demanding 'non-empty-string' is an improvement!
     */
    #[Override]
    public function get(string $id): mixed
    {
        if (! $this->pluginContainer->has($id)) {
            throw RenderingFailed::becauseAPluginDoesNotExist($id);
        }

        /** @var mixed $plugin */
        $plugin = $this->pluginContainer->get($id);
        if (! is_callable($plugin)) {
            throw RenderingFailed::becauseAPluginIsNotInvokable($id, $plugin);
        }

        return $plugin;
    }

    #[Override]
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
    #[Override]
    public function __call(string $method, array $args): mixed
    {
        $plugin = $this->get($method);

        try {
            /** @var mixed $returnValue */
            $returnValue = $plugin(...$args);
            $this->called[$method] = null;

            return $returnValue;
        } catch (Throwable $e) {
            throw RenderingFailed::becauseOfAPluginException($method, $e);
        }
    }

    #[Override]
    public function clearPluginState(): void
    {
        foreach (array_keys($this->called) as $name) {
            /** @var mixed $plugin */
            $plugin = $this->pluginContainer->get($name);
            if (! $plugin instanceof StatefulPlugin) {
                continue;
            }

            $plugin->resetState();
        }

        $this->called = [];
    }
}
