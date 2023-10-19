<?php

declare(strict_types=1);

namespace Looker\HTML;

use function ksort;

/**
 * phpcs:disable SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion
 *
 * @psalm-internal Looker
 * @psalm-immutable
 */
final readonly class Tag
{
    /** @var array<non-empty-lowercase-string, scalar> */
    public array $attributes;

    /**
     * @param non-empty-string $name
     * @param array<non-empty-lowercase-string, scalar> $attributes
     */
    public function __construct(
        public string $name,
        array $attributes,
        public string|null $content,
    ) {
        ksort($attributes);

        $this->attributes = $attributes;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name
            && $this->attributes === $other->attributes
            && $this->content === $other->content;
    }
}
