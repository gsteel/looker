<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use Looker\ConfigurationError;
use Looker\Template\AggregateResolver;
use Looker\Template\Resolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function array_map;
use function Psl\Type\array_key;
use function Psl\Type\dict;
use function Psl\Type\instance_of;
use function Psl\Type\non_empty_string;
use function Psl\Type\shape;

/** @internal */
final class AggregateResolverFactory
{
    /** @throws ConfigurationError */
    public function __invoke(ContainerInterface $container): AggregateResolver
    {
        try {
            $config = shape([
                'looker' => shape([
                    'templates' => shape([
                        'aggregate' => dict(array_key(), non_empty_string()),
                    ], true),
                ], true),
            ], true)->assert($container->has('config') ? $container->get('config') : []);
            $services = array_map(
                static fn (string $serviceName): Resolver => instance_of(Resolver::class)->assert(
                    $container->get($serviceName),
                ),
                $config['looker']['templates']['aggregate'],
            );
        } catch (Throwable) {
            throw new ConfigurationError(
                'The aggregate template resolver requires that the `config` array is present in the '
                . 'container, and that an array under the key `looker.templates.aggregate` is a list of strings that '
                . 'can be used to fetch other template resolver instances',
            );
        }

        return new AggregateResolver(...$services);
    }
}
