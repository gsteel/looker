<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\HtmlAttributes;
use Looker\Plugin\Javascript;
use Looker\PluginManager;
use Psr\Container\ContainerInterface;

final class JavascriptFactory
{
    public function __invoke(ContainerInterface $container): Javascript
    {
        $plugins = $container->get(PluginManager::class);

        return new Javascript(
            $plugins->get(HtmlAttributes::class),
        );
    }
}
