<?php

declare(strict_types=1);

namespace Looker\Plugin\Factory;

use GSteel\Dot;
use Looker\Plugin\BasePath;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function assert;

final class BasePathFactory
{
    public function __invoke(ContainerInterface $container): BasePath
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        Assert::isArray($config);

        $path = Dot::stringOrNull('looker.pluginConfig.basePath', $config) ?? '/';
        assert($path !== '');

        return new BasePath($path);
    }
}
