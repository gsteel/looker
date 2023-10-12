<?php

declare(strict_types=1);

namespace Looker\Template\Factory;

use GSteel\Dot;
use Looker\ConfigurationError;
use Looker\Template\DirectoryResolver;
use Psr\Container\ContainerInterface;
use Throwable;

use function Psl\Type\array_key;
use function Psl\Type\dict;
use function Psl\Type\mixed;
use function Psl\Type\non_empty_string;
use function Psl\Type\non_empty_vec;

final class DirectoryResolverFactory
{
    public function __invoke(ContainerInterface $container): DirectoryResolver
    {
        try {
            $config = dict(array_key(), mixed())->assert($container->get('config'));
            $list = Dot::array('looker.templates.paths', $config);
            $defaultSuffix = Dot::nonEmptyString('looker.templates.defaultSuffix', $config);
        } catch (Throwable) {
            throw new ConfigurationError(
                'The directory resolver requires that the `config` array is available in the container and '
                . 'that it has a) a list of directory paths under the key `looker.templates.paths` and, b) a non-empty '
                . 'string under the key `looker.templates.defaultSuffix` to use as the default template file name '
                . 'suffix.',
            );
        }

        if (! non_empty_vec(non_empty_string())->matches($list)) {
            throw new ConfigurationError(
                'The directory resolver requires a non-empty list of non-empty strings',
            );
        }

        return new DirectoryResolver($list, $defaultSuffix);
    }
}
