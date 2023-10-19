<?php

declare(strict_types=1);

namespace Looker\Model;

/** @psalm-immutable */
final readonly class ChildModel
{
    /**
     * @param non-empty-string $captureTo
     *
     * @throws TerminalModelCannotBeChild
     *
     * @psalm-internal Looker
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
