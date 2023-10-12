<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

final class FluentPlugin
{
    public function __invoke(): self
    {
        return $this;
    }

    public function getSheep(): string
    {
        return 'Bahhh';
    }
}
