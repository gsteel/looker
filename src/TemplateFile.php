<?php

declare(strict_types=1);

namespace Looker;

use Looker\Plugin\Escape;
use Looker\Plugin\HeadLink;
use Looker\Plugin\HeadMeta;
use Looker\Plugin\HeadStyle;
use Looker\Plugin\HeadTitle;
use Looker\Plugin\Javascript;
use Looker\Plugin\Placeholder;
use Looker\Value\Doctype as DoctypeEnum;

/**
 * Template File Auto-completion Helper Interface
 *
 * This interface should not be implemented. Its sole purpose is to provide an easy way for IDE's to provide
 * autocompletion and static type checks for plugin methods used in your template files.
 *
 * Within your template file, add a docblock annotation casting $this to the \Looker\Template type and your IDE should
 * do the rest of the work.
 *
 * If you write custom plugins, you can extend this template in your own projects, or create
 *
 * @psalm-suppress PossiblyUnusedMethod, UnusedClass
 */
interface TemplateFile
{
    /**
     * @param non-empty-string|null $file
     *
     * @return non-empty-string
     */
    public function basePath(string|null $file): string;

    /** @return value-of<DoctypeEnum> */
    public function doctype(string|DoctypeEnum|null $id = null): string;

    /** @return ($value is null ? Escape : string) */
    public function escape(string|null $value = null): Escape|string;

    public function headLink(): HeadLink;

    public function headMeta(): HeadMeta;

    public function headStyle(): HeadStyle;

    /** @param non-empty-string|null $title */
    public function headTitle(string|null $title = null): HeadTitle;

    /** @param array<array-key, mixed> $attributes */
    public function htmlAttributes(array $attributes): string;

    /** @param non-empty-string|null $layout */
    public function layout(string|null $layout): void;

    /**
     * @param non-empty-string $templateName
     * @param array<non-empty-string, mixed> $variables
     */
    public function partial(string $templateName, array $variables = []): string;

    /**
     * @param non-empty-string $templateName
     * @param list<array<non-empty-string, mixed>> $variables
     */
    public function partialLoop(string $templateName, array $variables = []): string;

    /**
     * @param non-empty-string|null $name
     *
     * @return ($name is null ? Placeholder : string)
     */
    public function placeholder(string|null $name = null): Placeholder|string;

    public function headScript(): Javascript;

    public function inlineScript(): Javascript;
}
