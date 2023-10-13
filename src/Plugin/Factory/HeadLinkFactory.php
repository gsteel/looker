<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadLink;
use Psr\Container\ContainerInterface;

final class HeadLinkFactory
{
    public function __invoke(ContainerInterface $container): HeadLink
    {
        return new HeadLink(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
            DefaultDoctype::retrieve($container),
        );
    }
}
