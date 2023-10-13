<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\Model\ViewModel;
use Looker\Template\Resolver;
use Looker\Template\TemplateCannotBeResolved;
use Psr\Container\ContainerInterface;

final readonly class PhpRenderer implements Renderer
{
    private PluginProxy $pluginProxy;

    public function __construct(
        private Resolver $resolver,
        private ContainerInterface $plugins,
        private bool $strictVariables = true,
        private bool $passScopeToChildren = false,
    ) {
        $this->pluginProxy = new PluginProxy($this->plugins);
    }

    /**
     * @throws RenderingFailed
     * @throws TemplateCannotBeResolved
     */
    public function render(ViewModel $model): string
    {
        foreach ($model->childModels() as $child) {
            $childModel = $child->model;
            if ($this->passScopeToChildren) {
                $childModel = $childModel->mergeRetain($model->variables());
            }

            $model = $model->withVariable($child->captureTo, $this->render($childModel));
        }

        $file = $this->resolver->resolve($model->template());

        return (new Target($file, $model->variables(), $this->pluginProxy, $this->strictVariables))();
    }

    public function clearPluginState(): void
    {
        $this->pluginProxy->clearPluginState();
    }
}
