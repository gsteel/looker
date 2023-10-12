<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\Template\MapResolver;
use Psr\Container\ContainerInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class MapResolverFactory
{
    public function __invoke(ContainerInterface $container): MapResolver
    {
        try {
            $config = $container->get('config');
            Assert::isArray($config);
            $map = Dot::array('looker.templates.map', $config);
            Assert::isMap($map);
            Assert::allStringNotEmpty($map);
            /** @psalm-var array<non-empty-string, non-empty-string> $map */
        } catch (Throwable) {
            throw new ConfigurationError(
                'The map resolver requires that `config` is an array available in the container and contains '
                . 'an array under the key `looker.templates.map` where all the keys and values are non-empty strings',
            );
        }

        return new MapResolver($map);
    }
}
