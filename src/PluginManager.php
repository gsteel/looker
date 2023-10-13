<?php

declare(strict_types=1);

namespace Looker;

use Looker\Renderer\RenderingFailed;
use Psr\Container\ContainerInterface;

interface PluginManager extends ContainerInterface
{
    public function clearPluginState(): void;

    /**
     * @param non-empty-string $method
     * @param array<string, mixed> $args
     *
     * @throws RenderingFailed If plugin retrieval or execution causes any exception.
     */
    public function __call(string $method, array $args): mixed;
}
