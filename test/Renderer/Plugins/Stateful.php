<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

use Looker\Plugin\StatefulPlugin;
use Override;
use Stringable;

use function implode;

final class Stateful implements StatefulPlugin, Stringable
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

    #[Override]
    public function resetState(): void
    {
        $this->values = [];
    }

    #[Override]
    public function __toString(): string
    {
        return implode(', ', $this->values);
    }
}
