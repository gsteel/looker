<?php

declare(strict_types=1);

namespace Looker\Model;

/** @psalm-immutable */
interface ViewModel
{
    /** @return non-empty-string */
    public function template(): string;

    /** @param non-empty-string $name */
    public function withTemplate(string $name): static;

    /** @return array<non-empty-string, mixed> */
    public function variables(): array;

    /** @return list<ChildModel> */
    public function childModels(): array;

    /** @param non-empty-string $name */
    public function withVariable(string $name, mixed $value): static;

    /**
     * Replace existing variables
     *
     * All existing variables are removed and replaced with the argument
     *
     * @param array<non-empty-string, mixed> $variables
     */
    public function replaceVariables(array $variables): static;

    /**
     * Merge additional variables into the model
     *
     * This operation performs a merge between the current variables and the given argument where variables in the
     * argument take precedence.
     *
     * @param array<non-empty-string, mixed> $variables
     */
    public function mergeReplace(array $variables): static;

    /**
     * Merge additional variables into the model
     *
     * This operation performs a merge between the current variables and the given argument where existing variables
     * take precedence.
     *
     * @param array<non-empty-string, mixed> $variables
     */
    public function mergeRetain(array $variables): static;

    /** @param non-empty-string $renderTo */
    public function withChild(self $model, string $renderTo): static;

    public function isTerminal(): bool;
}
