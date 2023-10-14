<?php

declare(strict_types=1);

namespace Looker\Plugin;

final class Layout implements StatefulPlugin
{
    /** @var non-empty-string|null */
    private string|null $layoutTemplate = null;

    public function resetState(): void
    {
        $this->layoutTemplate = null;
    }

    /** @param non-empty-string|null $layout */
    public function __invoke(string|null $layout): void
    {
        $this->layoutTemplate = $layout;
    }

    /** @return non-empty-string|null */
    public function currentLayout(): string|null
    {
        return $this->layoutTemplate;
    }
}
