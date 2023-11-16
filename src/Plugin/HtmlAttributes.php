<?php

declare(strict_types=1);

namespace Looker\Plugin;

use InvalidArgumentException;
use Laminas\Escaper\Escaper;

use function array_map;
use function implode;
use function is_array;
use function is_scalar;
use function sprintf;
use function str_contains;

final readonly class HtmlAttributes
{
    public function __construct(
        private Escaper $escaper,
    ) {
    }

    /** @param array<array-key, mixed> $attributes */
    public function __invoke(array $attributes): string
    {
        $attributeString = [];

        /** @psalm-suppress MixedAssignment */
        foreach ($attributes as $key => $value) {
            $key = $this->escaper->escapeHtml((string) $key);

            /** @psalm-assert-if-true array<array-key, scalar> $array */
            $allScalar = static function (array $array): bool {
                foreach ($array as $item) {
                    if (is_scalar($item)) {
                        continue;
                    }

                    return false;
                }

                return true;
            };

            // lists and maps of strings or numbers joined with a space, providing this is not a JS event handler
            if (is_array($value) && $allScalar($value)) {
                $value = implode(' ', array_map(static fn (string|int $value): string => (string) $value, $value));
            }

            if (! is_scalar($value)) {
                throw new InvalidArgumentException(
                    'HTML attribute arrays can only contain arrays with scalar values',
                );
            }

            if ($value === false) {
                continue;
            }

            if ($value === true) {
                $attributeString[] = $key;
                continue;
            }

            $value = $this->escaper->escapeHtmlAttr((string) $value);
            $quote = str_contains($value, '"') ? "'" : '"';
            $attributeString[] = sprintf('%2$s=%1$s%3$s%1$s', $quote, $key, $value);
        }

        return implode(' ', $attributeString);
    }
}
