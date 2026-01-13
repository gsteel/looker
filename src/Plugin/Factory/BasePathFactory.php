<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Plugin\BasePath;
use Psr\Container\ContainerInterface;

use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\union;

final class BasePathFactory
{
    public function __invoke(ContainerInterface $container): BasePath
    {
        $config = optional(shape([
            'looker' => optional(shape([
                'pluginConfig' => optional(shape([
                    'basePath' => optional(union(non_empty_string(), null())),
                ], true)),
            ], true)),
        ], true))->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        return new BasePath(
            $config['looker']['pluginConfig']['basePath'] ?? '/',
        );
    }
}
