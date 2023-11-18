<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\HeadStyle;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class HeadStyleFactory
{
    public function __invoke(ContainerInterface $container): HeadStyle
    {
        $plugins = $container->get(PluginManager::class);

        return new HeadStyle(
            $plugins->get(HtmlAttributes::class),
        );
    }
}
