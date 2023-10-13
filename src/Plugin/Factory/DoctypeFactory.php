<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\Doctype;
use Psr\Container\ContainerInterface;

final class DoctypeFactory
{
    public function __invoke(ContainerInterface $container): Doctype
    {
        return new Doctype(DefaultDoctype::retrieve($container));
    }
}
