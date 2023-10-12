<?php

declare(strict_types=1);

namespace Looker\Test\Renderer\Plugins;

use Exception;

final class Exceptional
{
    public function __invoke(): never
    {
        throw new Exception('Oh dear…');
    }
}
