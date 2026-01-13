<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Looker\Value\Doctype;
use Psr\Container\ContainerInterface;

use function Psl\Type\instance_of;
use function Psl\Type\null;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\union;

final class DefaultDoctype
{
    public static function retrieve(ContainerInterface $container): Doctype
    {
        $config = optional(shape([
            'looker' => optional(shape([
                'pluginConfig' => optional(shape([
                    'doctype' => optional(union(instance_of(Doctype::class), null())),
                ], true)),
            ], true)),
        ], true))->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        return $config['looker']['pluginConfig']['doctype'] ?? Doctype::HTML5;
    }
}
