<?php

declare(strict_types=1);

namespace Looker\Renderer;

use RuntimeException;
use Throwable;

use function get_debug_type;
use function sprintf;

final class RenderingFailed extends RuntimeException
{
    /** @param non-empty-string $filePath */
    public static function becauseTheTemplateFileCouldNotBeIncluded(string $filePath): self
    {
        return new self(sprintf(
            'Failed to render template because the template file could not be included: "%s"',
            $filePath,
        ));
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $filePath
     */
    public static function becauseOfAccessToAnUndeclaredVariable(string $name, string $filePath): self
    {
        return new self(sprintf(
            'Access to an undeclared variable "%s" in the template "%s"',
            $name,
            $filePath,
        ));
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $filePath
     */
    public static function becauseMemberVariablesCannotBeMutated(string $name, string $filePath): self
    {
        return new self(sprintf(
            'Attempt to mutate the variable "%s" in the template "%s"',
            $name,
            $filePath,
        ));
    }

    public static function becauseOfAnException(string $template, Throwable $error): self
    {
        return new self(sprintf(
            'An exception occurred during render of "%s" with the message: %s"',
            $template,
            $error->getMessage(),
        ), 0, $error);
    }

    /** @param non-empty-string $pluginName */
    public static function becauseAPluginDoesNotExist(string $pluginName): self
    {
        return new self(sprintf(
            'A plugin with the name "%s" could not be found in the plugin manager',
            $pluginName,
        ));
    }

    /** @param non-empty-string $pluginName */
    public static function becauseAPluginIsNotInvokable(string $pluginName, mixed $plugin): self
    {
        return new self(sprintf(
            'The plugin aliased to "%s" is not callable. Received a type of %s',
            $pluginName,
            get_debug_type($plugin),
        ));
    }

    /** @param non-empty-string $pluginName */
    public static function becauseOfAPluginException(string $pluginName, Throwable $error): self
    {
        return new self(sprintf(
            'An exception occurred during execution of the plugin "%s". Message: %s',
            $pluginName,
            $error->getMessage(),
        ), 0, $error);
    }

    /** @param non-empty-string $template */
    public static function becauseARenderLoopHasBeenDetected(string $template): self
    {
        return new self(sprintf(
            'A cyclic rendering dependency has been detected during render of the template "%s"',
            $template,
        ));
    }
}
