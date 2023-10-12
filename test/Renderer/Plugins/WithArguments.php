<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

use function implode;

final class WithArguments
{
    public function __invoke(string ...$values): string
    {
        return implode(' ', $values);
    }
}
