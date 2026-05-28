<?php

declare(strict_types=1);

namespace Looker\Template;

interface Resolver
{
    /**
     * Return the file path for the template with the given name.
     *
     * @param non-empty-string $name
     *
     * @return non-empty-string|false
     */
    public function resolve(string $name): string|false;
}
