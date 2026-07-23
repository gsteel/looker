<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\DirectoryResolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function Psl\Type\non_empty_string;
use function Psl\Type\non_empty_vec;
use function Psl\Type\shape;

/** @internal */
final class DirectoryResolverFactory
{
    /** @throws ConfigurationError */
    public function __invoke(ContainerInterface $container): DirectoryResolver
    {
        try {
            $config = shape([
                'looker' => shape([
                    'templates' => shape([
                        'paths' => non_empty_vec(non_empty_string()),
                        'defaultSuffix' => non_empty_string(),
                    ], true),
                ], true),
            ], true)->assert($container->has('config') ? $container->get('config') : null);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The directory resolver requires that the `config` array is available in the container and '
                . 'that it has a) a list of directory paths under the key `looker.templates.paths` and, b) a non-empty '
                . 'string under the key `looker.templates.defaultSuffix` to use as the default template file name '
                . 'suffix.',
            );
        }

        return new DirectoryResolver(
            $config['looker']['templates']['paths'],
            $config['looker']['templates']['defaultSuffix'],
        );
    }
}
