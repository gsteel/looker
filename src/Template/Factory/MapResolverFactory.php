<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\Template\MapResolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function Psl\Type\array_key;
use function Psl\Type\dict;
use function Psl\Type\mixed;
use function Psl\Type\non_empty_string;

final class MapResolverFactory
{
    public function __invoke(ContainerInterface $container): MapResolver
    {
        try {
            $config = dict(array_key(), mixed())->assert($container->get('config'));
            $map = Dot::array('looker.templates.map', $config);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The map resolver requires that `config` is an array available in the container and contains '
                . 'an array under the key `looker.templates.map`',
            );
        }

        if (! dict(non_empty_string(), non_empty_string())->matches($map)) {
            throw new ConfigurationError(
                'The template map resolver requires an array where all keys and values are non-empty strings',
            );
        }

        return new MapResolver($map);
    }
}
