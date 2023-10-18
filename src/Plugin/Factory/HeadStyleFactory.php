<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadStyle;
use Psr\Container\ContainerInterface;

final class HeadStyleFactory
{
    public function __invoke(ContainerInterface $container): HeadStyle
    {
        return new HeadStyle(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
        );
    }
}
