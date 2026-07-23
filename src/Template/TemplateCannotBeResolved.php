<?php

declare(strict_types=1);

namespace Looker\Template;

use RuntimeException;

use function sprintf;

final class TemplateCannotBeResolved extends RuntimeException
{
    /**
     * @param non-empty-string $message
     *
     * @psalm-internal Looker
     */
    public function __construct(
        string $message,
        public readonly Resolver $resolver,
    ) {
        parent::__construct($message);
    }

    /** @param non-empty-string $path */
    public static function becausePathIsNotADirectory(string $path, Resolver $resolver): self
    {
        return new self(sprintf(
            'The path provided for template resolution is not a directory: "%s"',
            $path,
        ), $resolver);
    }

    /** @param non-empty-string $name */
    public static function becauseTheNameContainsUpwardDirectoryTraversal(string $name, Resolver $resolver): self
    {
        return new self(sprintf(
            'The template name provided "%s" cannot be resolved because it includes upward directory traversal',
            $name,
        ), $resolver);
    }

    /** @param non-empty-string $name */
    public static function becauseAllResolversAreExhausted(string $name, Resolver $resolver): self
    {
        return new self(sprintf(
            'The template "%s" cannot be resolved because none of the configured resolvers could find it',
            $name,
        ), $resolver);
    }
}
