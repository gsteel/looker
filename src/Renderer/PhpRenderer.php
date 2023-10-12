<?php

declare(strict_types=1);

namespace Looker\Renderer;

use Looker\Model\ViewModel;
use Looker\Plugin\StatefulPlugin;
use Looker\Template\Resolver;
use Looker\Template\TemplateCannotBeResolved;
use Psr\Container\ContainerInterface;

final readonly class PhpRenderer implements Renderer
{
    public function __construct(
        private Resolver $resolver,
        private ContainerInterface $plugins,
        private bool $strictVariables = true,
        private bool $passScopeToChildren = false,
    ) {
    }

    public function render(ViewModel $model): string
    {
        $pluginProxy = new PluginProxy($this->plugins);

        $content = $this->renderModel($model, $pluginProxy);

        /**
         * After rendering, clear the state of any stateful plugins called
         */
        foreach ($pluginProxy->calledPlugins() as $alias) {
            $plugin = $this->plugins->get($alias);
            if (! $plugin instanceof StatefulPlugin) {
                continue;
            }

            $plugin->resetState();
        }

        // Maybe apply post rendering filter?

        return $content;
    }

    /**
     * @throws RenderingFailed
     * @throws TemplateCannotBeResolved
     */
    private function renderModel(ViewModel $model, PluginProxy $proxy): string
    {
        foreach ($model->childModels() as $child) {
            $childModel = $child->model;
            if ($this->passScopeToChildren) {
                $childModel = $childModel->mergeRetain($model->variables());
            }

            $model = $model->withVariable($child->captureTo, $this->renderModel($childModel, $proxy));
        }

        $file = $this->resolver->resolve($model->template());

        return (new Target($file, $model->variables(), $proxy, $this->strictVariables))();
    }
}
