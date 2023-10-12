<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

final class WorkingPlugin
{
    public function __invoke(): string
    {
        return 'It Worked!';
    }
}
