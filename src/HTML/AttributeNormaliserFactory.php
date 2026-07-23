<?php

declare(strict_types=1);

namespace Looker\HTML;

use Psr\Container\ContainerInterface;

use function Psl\Type\bool;
use function Psl\Type\optional;
use function Psl\Type\shape;

/** @psalm-internal Looker */
final readonly class AttributeNormaliserFactory
{
    public function __invoke(ContainerInterface $container): AttributeNormaliser
    {
        $config = optional(shape([
            'looker' => optional(shape([
                'permitUnknownAttributes' => optional(bool()),
            ], true)),
        ], true))->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        return new AttributeNormaliser(
            $config['looker']['permitUnknownAttributes'] ?? true,
        );
    }
}
