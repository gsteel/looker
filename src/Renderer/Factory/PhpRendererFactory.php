<?php

declare(strict_types=1);

namespace Looker\Renderer\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\PluginManager;
use Looker\Renderer\PhpRenderer;
use Looker\Template\Resolver;
use Psr\Container\ContainerInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class PhpRendererFactory
{
    public function __invoke(ContainerInterface $container): PhpRenderer
    {
        try {
            $config = $container->get('config');
            Assert::isArray($config);
            $strictVars = Dot::bool('looker.strictVariables', $config);
            $passScope = Dot::bool('looker.passScopeToChildren', $config);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The PhpRenderer requires that the `config` array can be retrieved from the container, and '
                . 'that it contains boolean values for the keys `looker.strictVariables` and '
                . '`looker.passScopeToChildren`',
            );
        }

        return new PhpRenderer(
            $container->get(Resolver::class),
            $container->get(PluginManager::class),
            $strictVars,
            $passScope,
        );
    }
}
