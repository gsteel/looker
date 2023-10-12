<?php

declare(strict_types=1);

namespace Looker\Template;

use function file_exists;
use function is_dir;
use function is_readable;
use function pathinfo;
use function rtrim;
use function sprintf;
use function str_contains;

use const DIRECTORY_SEPARATOR;
use const PATHINFO_EXTENSION;

final readonly class DirectoryResolver implements Resolver
{
    /**
     * @param non-empty-list<non-empty-string> $directories
     * @param non-empty-string $fileSuffix
     */
    public function __construct(
        private array $directories,
        private string $fileSuffix,
    ) {
    }

    public function resolve(string $name): string
    {
        foreach ($this->directories as $directory) {
            $path = $this->resolveFromDirectory($directory, $name);
            if ($path === null) {
                continue;
            }

            return $path;
        }

        throw TemplateCannotBeResolved::becauseItCannotBeFoundOnDisk($name, $this);
    }

    /**
     * @param non-empty-string $directory
     * @param non-empty-string $name
     *
     * @return non-empty-string|null
     */
    private function resolveFromDirectory(string $directory, string $name): string|null
    {
        if (str_contains($name, '..' . DIRECTORY_SEPARATOR)) {
            throw TemplateCannotBeResolved::becauseTheNameContainsUpwardDirectoryTraversal($name, $this);
        }

        if (! is_dir($directory)) {
            throw TemplateCannotBeResolved::becausePathIsNotADirectory($directory, $this);
        }

        if (pathinfo($name, PATHINFO_EXTENSION) === '') {
            $name .= '.' . $this->fileSuffix;
        }

        $file = sprintf('%s%s%s', rtrim($directory, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR, $name);
        if (! file_exists($file) || ! is_readable($file)) {
            return null;
        }

        return $file;
    }
}
