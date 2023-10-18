<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Laminas\Escaper\Escaper;
use Looker\HTML\StyleAttribute;
use Looker\HTML\Tag;

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

final class HeadStyle implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $styles = [];
    private string $separator;

    public function __construct(
        private readonly Escaper $escaper,
        private readonly string $defaultSeparator = "\n\t",
    ) {
        $this->separator = $this->defaultSeparator;
    }

    public function resetState(): void
    {
        $this->styles = [];
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

    /** @param array<non-empty-string, scalar> $tagAttributes */
    public function append(string $style, array $tagAttributes = []): self
    {
        $tag = $this->makeTag($style, $tagAttributes);
        $this->remove($tag);
        $this->styles[] = $tag;

        return $this;
    }

    /** @param array<non-empty-string, scalar> $tagAttributes */
    public function prepend(string $style, array $tagAttributes = []): self
    {
        $tag = $this->makeTag($style, $tagAttributes);
        $this->remove($tag);
        array_unshift($this->styles, $tag);

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    private function makeTag(string|null $styles, array $attributes): Tag
    {
        return new Tag('style', $this->normaliseAttributes($attributes), $styles);
    }

    private function remove(Tag $tag): void
    {
        $styles = $this->styles;

        foreach ($styles as $key => $item) {
            if (! $item->equals($tag)) {
                continue;
            }

            unset($styles[$key]);
            break;
        }

        $this->styles = array_values($styles);
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
            if (StyleAttribute::isBoolean($name)) {
                if ($value === false) {
                    continue;
                }

                $result[$name] = true;
                continue;
            }

            if (! StyleAttribute::exists($name)) {
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

        if ($attributeString === [] && ($tag->content === '' || $tag->content === null)) {
            return '';
        }

        $content = (string) $tag->content;
        $content = $content !== '' ? "\n" . $content . "\n" : '';

        $attributeString = $attributeString === []
            ? ''
            : ' ' . implode(' ', $attributeString);

        return sprintf(
            '<%s%s>%s</%s>',
            $tag->name,
            $attributeString,
            $content,
            $tag->name,
        );
    }

    public function toString(): string
    {
        return implode($this->separator, array_filter(array_map(
            fn (Tag $tag): string => $this->tagToString($tag),
            $this->styles,
        )));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
