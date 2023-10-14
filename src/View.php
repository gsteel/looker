<?php

declare(strict_types=1);

namespace Looker;

use Looker\Model\Model;
use Looker\Model\ViewModel;
use Looker\Plugin\Layout;
use Looker\Renderer\Renderer;
use Looker\Renderer\RenderingFailed;

use function is_array;
use function is_string;
use function sprintf;

final readonly class View
{
    /**
     * @param non-empty-string|null $defaultLayout
     * @param non-empty-string $captureTo
     */
    public function __construct(
        private Renderer $renderer,
        private PluginManager $plugins,
        private string|null $defaultLayout = null,
        private string $captureTo = 'content',
    ) {
    }

    /**
     * @param array<non-empty-string, mixed>|ViewModel $viewModel
     * @param non-empty-string|null $template
     */
    public function render(array|ViewModel $viewModel, string|null $template = null): string
    {
        $viewModel = is_array($viewModel) && is_string($template)
            ? Model::new($template, $viewModel)
            : $viewModel;

        if (! $viewModel instanceof ViewModel) {
            throw new RenderingFailed(sprintf(
                '%s accepts either a configured view model as its only argument, or, '
                . 'an array of template variables and a template name',
                __METHOD__,
            ));
        }

        $layout = $this->resolveLayout($viewModel);
        if ($layout === false) {
            $buffer = $this->renderer->render($viewModel);
        } else {
            $buffer = $this->renderer->render(
                Model::terminal($layout)
                    ->withChild($viewModel, $this->captureTo),
            );
        }

        $this->plugins->clearPluginState();

        return $buffer;
    }

    /** @return non-empty-string|false */
    private function resolveLayout(ViewModel $model): string|false
    {
        // Layout template set in the layout plugin takes precedence
        $plugin = $this->plugins->has(Layout::class)
            ? $this->plugins->get(Layout::class)
            : null;

        $layout = $plugin instanceof Layout
            ? $plugin->currentLayout()
            : null;

        if ($layout !== null) {
            return $layout;
        }

        // A custom layout in the view model has next precedence
        /** @psalm-suppress MixedAssignment */
        $custom = $model->variables()['layout'] ?? null;
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }

        if ($custom === false || $this->defaultLayout === null) {
            return false;
        }

        return $this->defaultLayout;
    }
}
