<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\Model\Model;
use Looker\Renderer\Renderer;

final readonly class Partial
{
    public function __construct(private Renderer $renderer)
    {
    }

    /**
     * @param non-empty-string $templateName
     * @param array<non-empty-string, mixed> $variables
     */
    public function __invoke(string $templateName, array $variables = []): string
    {
        return $this->renderer->render(Model::new(
            $templateName,
            $variables,
        ));
    }
}
