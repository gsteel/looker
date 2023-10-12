<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Looker\Plugin\HtmlAttributes;
use Psr\Container\ContainerInterface;

final class HtmlAttributesFactory
{
    public function __invoke(ContainerInterface $container): HtmlAttributes
    {
        return new HtmlAttributes($container->get(Escaper::class));
    }
}
