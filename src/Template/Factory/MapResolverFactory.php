<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\MapResolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function Psl\Type\dict;
use function Psl\Type\non_empty_string;
use function Psl\Type\shape;

/**
 * @internal
 * @internal
 */
final class MapResolverFactory
{
    /** @throws ConfigurationError */
    public function __invoke(ContainerInterface $container): MapResolver
    {
        try {
            $config = shape([
                'looker' => shape([
                    'templates' => shape([
                        'map' => dict(non_empty_string(), non_empty_string()),
                    ], true),
                ], true),
            ], true)->assert($container->has('config') ? $container->get('config') : null);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The map resolver requires that `config` is an array available in the container and contains '
                . 'an array under the key `looker.templates.map` where all the keys and values are non-empty strings',
            );
        }

        return new MapResolver($config['looker']['templates']['map']);
    }
}
