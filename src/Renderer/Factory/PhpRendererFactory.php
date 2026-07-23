<?php

declare(strict_types=1);

namespace Looker\Renderer\Factory;

use Looker\ConfigurationError;
use Looker\PluginManager;
use Looker\Renderer\PhpRenderer;
use Looker\Template\Resolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function Psl\Type\bool;
use function Psl\Type\shape;

/** @internal */
final class PhpRendererFactory
{
    /**
     * @throws ConfigurationError
     */
    public function __invoke(ContainerInterface $container): PhpRenderer
    {
        try {
            $config = shape([
                'looker' => shape([
                    'strictVariables' => bool(),
                    'passScopeToChildren' => bool(),
                ], true),
            ], true)->assert(
                $container->has('config')
                    ? $container->get('config')
                    : [],
            );
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
            $config['looker']['strictVariables'],
            $config['looker']['passScopeToChildren'],
        );
    }
}
