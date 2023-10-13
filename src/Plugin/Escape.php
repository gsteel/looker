<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Laminas\Escaper\Escaper;

final readonly class Escape
{
    public function __construct(public Escaper $escaper)
    {
    }

    /** @return ($value is null ? self : string) */
    public function __invoke(string|null $value = null): self|string
    {
        return $value === null ? $this : $this->escaper->escapeHtml($value);
    }

    public function html(string $value): string
    {
        return $this->escaper->escapeHtml($value);
    }

    public function js(string $value): string
    {
        return $this->escaper->escapeJs($value);
    }

    public function css(string $value): string
    {
        return $this->escaper->escapeCss($value);
    }

    public function url(string $value): string
    {
        return $this->escaper->escapeUrl($value);
    }

    public function attribute(string $value): string
    {
        return $this->escaper->escapeHtmlAttr($value);
    }
}
