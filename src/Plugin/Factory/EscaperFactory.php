<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Psr\Container\ContainerInterface;

use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\union;

final class EscaperFactory
{
    public function __invoke(ContainerInterface $container): Escaper
    {
        $config = optional(shape([
            'looker' => optional(shape([
                'encoding' => optional(union(non_empty_string(), null())),
            ], true)),
        ], true))->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        return new Escaper($config['looker']['encoding'] ?? 'utf-8');
    }
}
