<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\Placeholder;

final class PlaceholderFactory
{
    public function __invoke(): Placeholder
    {
        return new Placeholder();
    }
}
