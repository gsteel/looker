<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\Model\ViewModel;

interface Renderer
{
    /** @throws RenderingFailed */
    public function render(ViewModel $model): string;
}
