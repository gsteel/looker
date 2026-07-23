<?php

declare(strict_types=1);

namespace Looker\Model;

/** @immutable */
final readonly class ChildModel
{
    /**
     * @param non-empty-string $captureTo
     *
     * @throws TerminalModelCannotBeChild
     *
     * @internal
     */
    public function __construct(
        public ViewModel $model,
        public string $captureTo,
    ) {
        if ($this->model->isTerminal()) {
            throw TerminalModelCannotBeChild::with($this->captureTo);
        }
    }
}
