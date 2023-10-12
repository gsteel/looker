<?php

declare(strict_types=1);

namespace Looker\Template;

use function array_key_exists;

final readonly class MapResolver implements Resolver
{
    /** @param array<non-empty-string, non-empty-string> $map */
    public function __construct(private array $map)
    {
    }

    public function resolve(string $name): string
    {
        if (! array_key_exists($name, $this->map)) {
            throw TemplateCannotBeResolved::becauseItWasNotConfigured($name, $this);
        }

        return $this->map[$name];
    }
}
