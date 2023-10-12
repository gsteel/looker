<?php

declare(strict_types=1);

namespace Looker\Plugin;

interface StatefulPlugin
{
    public function resetState(): void;
}
