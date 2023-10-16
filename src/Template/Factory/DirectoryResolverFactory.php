<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\Template\DirectoryResolver;
use Psr\Container\ContainerInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class DirectoryResolverFactory
{
    public function __invoke(ContainerInterface $container): DirectoryResolver
    {
        try {
            $config = $container->has('config') ? $container->get('config') : null;
            Assert::isArray($config);
            $list = Dot::array('looker.templates.paths', $config);
            Assert::isList($list);
            Assert::notEmpty($list);
            Assert::allStringNotEmpty($list);
            $defaultSuffix = Dot::nonEmptyString('looker.templates.defaultSuffix', $config);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The directory resolver requires that the `config` array is available in the container and '
                . 'that it has a) a list of directory paths under the key `looker.templates.paths` and, b) a non-empty '
                . 'string under the key `looker.templates.defaultSuffix` to use as the default template file name '
                . 'suffix.',
            );
        }

        return new DirectoryResolver($list, $defaultSuffix);
    }
}
