<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Escape;
use Psr\Container\ContainerInterface;

final class EscapeFactory
{
    public function __invoke(ContainerInterface $container): Escape
    {
        return new Escape(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
        );
    }
}
