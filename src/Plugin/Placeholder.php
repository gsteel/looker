<?php

declare(strict_types=1);

namespace Looker\Plugin;

use function array_unshift;
use function implode;

final class Placeholder implements StatefulPlugin
{
    /** @var array<string, list<string>> */
    private array $data = [];
    private string $separator = '';
    /** @var array<string, string> */
    private array $separators = [];

    public function resetState(): void
    {
        $this->data = [];
        $this->separator = '';
        $this->separators = [];
    }

    /**
     * @param non-empty-string|null $name
     *
     * @return ($name is null ? self : string)
     */
    public function __invoke(
        string|null $name = null,
    ): self|string {
        if ($name !== null) {
            return $this->toString($name);
        }

        return $this;
    }

    public function setSeparator(string $separator, string|null $name = null): self
    {
        if ($name === null) {
            $this->separator = $separator;

            return $this;
        }

        $this->separators[$name] = $separator;

        return $this;
    }

    /** @param non-empty-string $name */
    public function clear(string $name): self
    {
        unset($this->data[$name]);

        return $this;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $value
     */
    public function set(string $name, string $value): self
    {
        $this->data[$name] = [$value];

        return $this;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $value
     */
    public function append(string $name, string $value): self
    {
        if (! isset($this->data[$name])) {
            $this->data[$name] = [];
        }

        $this->data[$name][] = $value;

        return $this;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $value
     */
    public function prepend(string $name, string $value): self
    {
        $list = $this->data[$name] ?? [];
        array_unshift($list, $value);

        $this->data[$name] = $list;

        return $this;
    }

    public function toString(string $name): string
    {
        $separator = $this->separators[$name] ?? $this->separator;

        return isset($this->data[$name])
            ? implode($separator, $this->data[$name])
            : '';
    }
}
