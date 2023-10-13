<?php

declare(strict_types=1);

namespace Looker\Plugin;

use function ltrim;
use function rtrim;
use function sprintf;

final readonly class BasePath
{
    /** @param non-empty-string $basePath */
    public function __construct(private string $basePath = '/')
    {
    }

    /**
     * @param non-empty-string|null $file
     *
     * @return non-empty-string
     */
    public function __invoke(string|null $file = null): string
    {
        return $file === null ? $this->basePath : sprintf(
            '%s/%s',
            rtrim($this->basePath, '/'),
            ltrim($file, '/'),
        );
    }
}
