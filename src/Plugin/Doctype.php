<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\Value\Doctype as DoctypeEnum;
use Throwable;

use function assert;
use function constant;
use function sprintf;

final readonly class Doctype
{
    public function __construct(public DoctypeEnum $default)
    {
    }

    /** @return value-of<DoctypeEnum> */
    public function __invoke(string|DoctypeEnum|null $id = null): string
    {
        if ($id === null) {
            return $this->default->value;
        }

        if ($id instanceof DoctypeEnum) {
            return $id->value;
        }

        try {
            $enum = constant(sprintf('%s::%s', DoctypeEnum::class, $id));
            assert($enum instanceof DoctypeEnum);

            return $enum->value;
        } catch (Throwable) {
            return $this->default->value;
        }
    }
}
