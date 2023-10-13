<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use GSteel\Dot;
use Laminas\Escaper\Escaper;
use Looker\Plugin\HeadTitle;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class HeadTitleFactory
{
    public function __invoke(ContainerInterface $container): HeadTitle
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        $separator = Dot::stringOrNull('looker.pluginConfig.headTitle.separator', $config);
        $fallback = Dot::stringOrNull('looker.pluginConfig.headTitle.fallbackTitle', $config);

        return new HeadTitle(
            $container->has(Escaper::class)
                ? $container->get(Escaper::class)
                : new Escaper(),
            $separator === '' ? null : $separator,
            $fallback === '' ? null : $fallback,
        );
    }
}
