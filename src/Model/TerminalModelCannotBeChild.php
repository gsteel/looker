<?php

declare(strict_types=1);

namespace Looker\Model;

use InvalidArgumentException;

use function sprintf;

final class TerminalModelCannotBeChild extends InvalidArgumentException
{
    /** @param non-empty-string $captureTo */
    public static function with(string $captureTo): self
    {
        return new self(sprintf(
            'Attempting to capture a child model to the variable "%s" when the model is terminal',
            $captureTo,
        ));
    }
}
