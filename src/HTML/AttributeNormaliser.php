<?php

declare(strict_types=1);

namespace Looker\HTML;

use function array_change_key_case;
use function ksort;

use const CASE_LOWER;

/** @psalm-internal Looker */
final class AttributeNormaliser
{
    /**
     * @param array<string, scalar|null> $attributes
     *
     * @return array<non-empty-lowercase-string, scalar>
     *
     * @psalm-suppress MixedAssignment
     */
    public static function normalise(array $attributes, AttributeInformation $info): array
    {
        /** @psalm-var array<non-empty-lowercase-string, mixed> $attributes */
        $attributes = array_change_key_case($attributes, CASE_LOWER);

        $result = [];

        foreach ($attributes as $name => $value) {
            if ($info::isBoolean($name)) {
                if ($value === false) {
                    continue;
                }

                $result[$name] = true;
                continue;
            }

            if (! $info::exists($name)) {
                continue;
            }

            $result[$name] = (string) $value;
        }

        ksort($result);

        return $result;
    }
}
