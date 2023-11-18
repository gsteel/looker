<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\HeadLink;
use Looker\Plugin\HtmlAttributes;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class HeadLinkFactory
{
    public function __invoke(ContainerInterface $container): HeadLink
    {
        $plugins = $container->get(PluginManager::class);

        return new HeadLink(
            DefaultDoctype::retrieve($container),
            $plugins->get(HtmlAttributes::class),
        );
    }
}
