<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Javascript;
use Psr\Container\ContainerInterface;

final class JavascriptFactory
{
    public function __invoke(ContainerInterface $container): Javascript
    {
        return new Javascript(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
        );
    }
}
