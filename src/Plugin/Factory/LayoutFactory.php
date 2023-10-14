<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\Layout;

final class LayoutFactory
{
    public function __invoke(): Layout
    {
        return new Layout();
    }
}
