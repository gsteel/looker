<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\StyleAttribute;
use Looker\HTML\Tag;

use function array_filter;
use function array_map;
use function array_unshift;
use function array_values;
use function implode;
use function sprintf;

final class HeadStyle implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $styles = [];
    private string $separator;

    public function __construct(
        private readonly HtmlAttributes $attributePlugin,
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

    /**
     * @param non-empty-string $style
     * @param array<non-empty-string, scalar> $tagAttributes
     */
    public function append(string $style, array $tagAttributes = []): self
    {
        $tag = $this->makeTag($style, $tagAttributes);
        $this->remove($tag);
        $this->styles[] = $tag;

        return $this;
    }

    /**
     * @param non-empty-string $style
     * @param array<non-empty-string, scalar> $tagAttributes
     */
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
        return new Tag(
            'style',
            AttributeNormaliser::normalise($attributes, new StyleAttribute()),
            $styles,
        );
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

    private function tagToString(Tag $tag): string
    {
        $attributes = ($this->attributePlugin)($tag->attributes);

        if ($attributes === '' && ($tag->content === '' || $tag->content === null)) {
            return '';
        }

        $content = (string) $tag->content;
        $content = $content !== '' ? "\n" . $content . "\n" : '';

        return sprintf(
            '<%s%s>%s</%s>',
            $tag->name,
            $attributes !== '' ? ' ' . $attributes : '',
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
