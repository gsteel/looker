<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\PartialLoop;
use Looker\Renderer\Renderer;
use Psr\Container\ContainerInterface;

final class PartialLoopFactory
{
    public function __invoke(ContainerInterface $container): PartialLoop
    {
        return new PartialLoop($container->get(Renderer::class));
    }
}
