<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\Template\AggregateResolver;
use Looker\Template\Resolver;
use Psr\Container\ContainerInterface;
use Throwable;
use Webmozart\Assert\Assert;

use function array_map;

final class AggregateResolverFactory
{
    public function __invoke(ContainerInterface $container): AggregateResolver
    {
        try {
            $config = $container->get('config');
            Assert::isArray($config);
            $serviceNames = Dot::array('looker.templates.aggregate', $config);
            $services = array_map(static function (string $serviceName) use ($container): Resolver {
                $service = $container->get($serviceName);
                Assert::isInstanceOf($service, Resolver::class);

                return $service;
            }, $serviceNames);
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
