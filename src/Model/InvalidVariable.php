<?php

declare(strict_types=1);

namespace Looker\Model;

use InvalidArgumentException;

use function sprintf;

final class InvalidVariable extends InvalidArgumentException
{
    public static function forKey(string|int $key): self
    {
        return new self(sprintf(
            'Variables extracted from iterables or objects cannot have integer or empty string keys. '
            . 'Received "%s"',
            (string) $key,
        ));
    }
}
