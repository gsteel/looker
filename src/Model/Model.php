<?php

declare(strict_types=1);

namespace Looker\Model;

use function array_merge;

/** @psalm-immutable */
final readonly class Model implements ViewModel
{
    /**
     * @param non-empty-string $template
     * @param array<non-empty-string, mixed> $variables
     * @param list<ChildModel> $childModels
     */
    private function __construct(
        private string $template,
        private array $variables,
        private array $childModels,
        private bool $terminal,
    ) {
    }

    /**
     * @param non-empty-string $template
     * @param array<non-empty-string, mixed> $variables
     */
    public static function new(string $template, array $variables = []): self
    {
        return new self($template, $variables, [], false);
    }

    /**
     * @param non-empty-string $template
     * @param array<non-empty-string, mixed> $variables
     */
    public static function terminal(string $template, array $variables = []): self
    {
        return new self($template, $variables, [], true);
    }

    public function template(): string
    {
        return $this->template;
    }

    /** @param non-empty-string $name */
    public function withTemplate(string $name): static
    {
        return new self(
            $name,
            $this->variables,
            $this->childModels,
            $this->terminal,
        );
    }

    /** @inheritDoc */
    public function variables(): array
    {
        return $this->variables;
    }

    /** @inheritDoc */
    public function childModels(): array
    {
        return $this->childModels;
    }

    public function withVariable(string $name, mixed $value): static
    {
        $variables = $this->variables;
        /** @psalm-suppress MixedAssignment */
        $variables[$name] = $value;

        return new self(
            $this->template,
            $variables,
            $this->childModels,
            $this->terminal,
        );
    }

    /** @inheritDoc */
    public function replaceVariables(array $variables): static
    {
        return new self(
            $this->template,
            $variables,
            $this->childModels,
            $this->terminal,
        );
    }

    /** @inheritDoc */
    public function mergeReplace(array $variables): static
    {
        return new self(
            $this->template,
            array_merge($this->variables, $variables),
            $this->childModels,
            $this->terminal,
        );
    }

    /** @inheritDoc */
    public function mergeRetain(array $variables): static
    {
        return new self(
            $this->template,
            array_merge($variables, $this->variables),
            $this->childModels,
            $this->terminal,
        );
    }

    public function withChild(ViewModel $model, string $renderTo): static
    {
        $models = $this->childModels;
        $models[] = new ChildModel($model, $renderTo);

        return new self(
            $this->template,
            $this->variables,
            $models,
            $this->terminal,
        );
    }

    public function isTerminal(): bool
    {
        return $this->terminal;
    }
}
