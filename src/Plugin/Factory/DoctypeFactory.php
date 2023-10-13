<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use GSteel\Dot;
use Looker\Plugin\Doctype;
use Looker\Value\Doctype as DoctypeEnum;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class DoctypeFactory
{
    public function __invoke(ContainerInterface $container): Doctype
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        /** @psalm-var mixed $default */
        $default = Dot::valueOrNull('looker.pluginConfig.doctype', $config);
        $default = $default instanceof DoctypeEnum ? $default : DoctypeEnum::HTML5;

        return new Doctype($default);
    }
}
