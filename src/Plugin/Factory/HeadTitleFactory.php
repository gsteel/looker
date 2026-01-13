<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use Laminas\Escaper\Escaper;
use Laminas\Escaper\EscaperInterface;
use Looker\Plugin\HeadTitle;
use Psr\Container\ContainerInterface;

use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\union;

final class HeadTitleFactory
{
    public function __invoke(ContainerInterface $container): HeadTitle
    {
        $config = optional(shape([
            'looker' => optional(shape([
                'pluginConfig' => optional(shape([
                    'headTitle' => optional(shape([
                        'separator' => union(non_empty_string(), null()),
                        'fallbackTitle' => union(non_empty_string(), null()),
                    ], true)),
                ], true)),
            ], true)),
        ], true))->assert(
            $container->has('config')
                ? $container->get('config')
                : [],
        );

        return new HeadTitle(
            $container->has(EscaperInterface::class)
                ? $container->get(EscaperInterface::class)
                : new Escaper(),
            $config['looker']['pluginConfig']['headTitle']['separator'] ?? null,
            $config['looker']['pluginConfig']['headTitle']['fallbackTitle'] ?? null,
        );
    }
}
