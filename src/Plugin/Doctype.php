<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\Value\Doctype as DoctypeEnum;
use Psl\Type;
use Throwable;

use function constant;
use function sprintf;

final readonly class Doctype
{
    public function __construct(
        public DoctypeEnum $default,
    ) {
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
            $enum = Type\instance_of(DoctypeEnum::class)->assert(
                constant(sprintf('%s::%s', DoctypeEnum::class, $id)),
            );

            return $enum->value;
        } catch (Throwable) {
            return $this->default->value;
        }
    }
}
