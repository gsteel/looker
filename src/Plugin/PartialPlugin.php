<?php

declare(strict_types=1);

namespace Looker\Plugin;

interface PartialPlugin
{
    /**
     * Render a template with its own variable scope
     *
     * @param non-empty-string $templateName
     * @param array<non-empty-string, mixed> $variables
     */
    public function __invoke(string $templateName, array $variables = []): string;
}
