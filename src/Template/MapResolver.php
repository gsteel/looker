<?php

declare(strict_types=1);

namespace Looker\Template;

use Override;

use function array_key_exists;

final readonly class MapResolver implements Resolver
{
    /** @param array<non-empty-string, non-empty-string> $map */
    public function __construct(
        private array $map,
    ) {
    }

    #[Override]
    public function resolve(string $name): string|false
    {
        if (! array_key_exists($name, $this->map)) {
            return false;
        }

        return $this->map[$name];
    }
}
