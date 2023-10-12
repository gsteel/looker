<?php

declare(strict_types=1);

namespace Looker\Test\Integration\ResetPluginState\Plugins;

use Looker\Plugin\StatefulPlugin;

use function implode;

final class Stateful implements StatefulPlugin
{
    /** @var list<string> */
    private array $values = [];

    public function __invoke(): self
    {
        return $this;
    }

    public function add(string $value): self
    {
        $this->values[] = $value;

        return $this;
    }

    public function resetState(): void
    {
        $this->values = [];
    }

    public function __toString(): string
    {
        return implode(', ', $this->values);
    }
}
