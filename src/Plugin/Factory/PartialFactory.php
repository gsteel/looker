<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\Partial;
use Looker\Renderer\Renderer;
use Psr\Container\ContainerInterface;

final class PartialFactory
{
    public function __invoke(ContainerInterface $container): Partial
    {
        return new Partial($container->get(Renderer::class));
    }
}
