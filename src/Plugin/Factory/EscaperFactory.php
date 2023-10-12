<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use GSteel\Dot;
use Laminas\Escaper\Escaper;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class EscaperFactory
{
    public function __invoke(ContainerInterface $container): Escaper
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        return new Escaper(Dot::stringDefault('looker.encoding', $config, 'utf-8'));
    }
}
