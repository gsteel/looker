<?php

declare(strict_types=1);

namespace Looker\Model;

/** @psalm-immutable */
final readonly class ChildModel
{
    /**
     * @internal
     *
     * @param non-empty-string $captureTo
     *
     * @throws TerminalModelCannotBeChild
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
