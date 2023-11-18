<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\HeadMeta;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class HeadMetaFactory
{
    public function __invoke(ContainerInterface $container): HeadMeta
    {
        $plugins = $container->get(PluginManager::class);

        return new HeadMeta(
            DefaultDoctype::retrieve($container),
            $plugins->get(HtmlAttributes::class),
        );
    }
}
