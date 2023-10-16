<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Laminas\Escaper\Escaper;
use Looker\HTML\MetaAttribute;
use Looker\HTML\Tag;
use Looker\Value\Doctype;

use function array_change_key_case;
use function array_filter;
use function array_map;
use function array_unshift;
use function array_values;
use function implode;
use function is_bool;
use function sprintf;
use function str_contains;

use const CASE_LOWER;

final class HeadMeta implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $meta = [];
    private string $separator;

    public function __construct(
        private readonly Escaper $escaper,
        private readonly Doctype $doctype,
        private readonly string $defaultSeparator = "\n\t",
    ) {
        $this->separator = $this->defaultSeparator;
    }

    public function resetState(): void
    {
        $this->meta = [];
        $this->separator = $this->defaultSeparator;
    }

    public function __invoke(): self
    {
        return $this;
    }

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    public function append(array $attributes): self
    {
        $tag = $this->makeTag($attributes);
        $this->remove($tag);
        $this->meta[] = $tag;

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    public function prepend(array $attributes): self
    {
        $tag = $this->makeTag($attributes);
        $this->remove($tag);
        array_unshift($this->meta, $tag);

        return $this;
    }

    public function removeWithAttributeMatching(string $attribute, mixed $value): self
    {
        foreach ($this->meta as $key => $item) {
            if (! isset($item->attributes[$attribute])) {
                continue;
            }

            if ($item->attributes[$attribute] !== $value) {
                continue;
            }

            unset($this->meta[$key]);
        }

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    private function makeTag(array $attributes): Tag
    {
        return new Tag('meta', $this->normaliseAttributes($attributes), null);
    }

    private function remove(Tag $tag): void
    {
        $meta = $this->meta;

        foreach ($meta as $key => $item) {
            if (! $item->equals($tag)) {
                continue;
            }

            unset($meta[$key]);
            break;
        }

        $this->meta = array_values($meta);
    }

    /**
     * @param array<non-empty-string, scalar> $attributes
     *
     * @return array<non-empty-lowercase-string, scalar>
     */
    private function normaliseAttributes(array $attributes): array
    {
        /** @psalm-var array<non-empty-lowercase-string, scalar> $attributes */
        $attributes = array_change_key_case($attributes, CASE_LOWER);

        $result = [];

        foreach ($attributes as $name => $value) {
            if (MetaAttribute::isBoolean($name)) {
                if ($value === false) {
                    continue;
                }

                $result[$name] = true;
                continue;
            }

            if (! MetaAttribute::exists($name)) {
                continue;
            }

            $result[$name] = $value;
        }

        return $result;
    }

    private function tagToString(Tag $tag): string
    {
        $attributes = $tag->attributes;
        $attributeString = [];

        foreach ($attributes as $name => $value) {
            // Omit boolean values that are explicitly set to false
            if (is_bool($value)) {
                $attributeString[] = $this->escaper->escapeHtml($name);

                continue;
            }

            $quote = str_contains((string) $value, '"') ? "'" : '"';
            $attributeString[] = sprintf(
                '%2$s=%1$s%3$s%1$s',
                $quote,
                $this->escaper->escapeHtml($name),
                $this->escaper->escapeHtmlAttr((string) $value),
            );
        }

        if ($attributeString === []) {
            return '';
        }

        return sprintf(
            '<%s %s%s>',
            $tag->name,
            implode(' ', $attributeString),
            $this->doctype->isXhtml() ? ' /' : '',
        );
    }

    public function toString(): string
    {
        return implode($this->separator, array_filter(array_map(
            fn (Tag $tag): string => $this->tagToString($tag),
            $this->meta,
        )));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
