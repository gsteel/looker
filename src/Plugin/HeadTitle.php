<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Laminas\Escaper\Escaper;

use function array_unshift;
use function implode;
use function sprintf;

final class HeadTitle implements StatefulPlugin
{
    private const DEFAULT_SEPARATOR = ' - ';

    /** @var list<non-empty-string> */
    private array $title = [];
    private string $separator;
    /** @var non-empty-string */
    private readonly string $defaultSeparator;

    /**
     * @param non-empty-string|null $defaultSeparator
     * @param non-empty-string|null $fallbackTitle
     */
    public function __construct(
        private readonly Escaper $escaper,
        string|null $defaultSeparator = self::DEFAULT_SEPARATOR,
        private readonly string|null $fallbackTitle = null,
    ) {
        $this->defaultSeparator = $defaultSeparator ?? self::DEFAULT_SEPARATOR;
        $this->separator = $this->defaultSeparator;
    }

    public function resetState(): void
    {
        $this->title = [];
        $this->separator = $this->defaultSeparator;
    }

    /** @param non-empty-string|null $title */
    public function __invoke(string|null $title = null): self
    {
        if ($title !== null) {
            $this->append($title);
        }

        return $this;
    }

    /** @param non-empty-string $title */
    public function set(string $title): self
    {
        $this->title = [$title];

        return $this;
    }

    /** @param non-empty-string $title */
    public function append(string $title): self
    {
        $this->title[] = $title;

        return $this;
    }

    /** @param non-empty-string $title */
    public function prepend(string $title): self
    {
        array_unshift($this->title, $title);

        return $this;
    }

    /** @param non-empty-string $separator */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    public function toString(): string
    {
        $title = implode($this->separator, $this->title);

        if ($title === '') {
            $title = (string) $this->fallbackTitle;
        }

        return sprintf('<title>%s</title>', $this->escaper->escapeHtml($title));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
