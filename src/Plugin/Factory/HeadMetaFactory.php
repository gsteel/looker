<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadMeta;
use Psr\Container\ContainerInterface;

final class HeadMetaFactory
{
    public function __invoke(ContainerInterface $container): HeadMeta
    {
        return new HeadMeta(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
            DefaultDoctype::retrieve($container),
        );
    }
}
