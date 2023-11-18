<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\MetaAttribute;
use Looker\HTML\Tag;
use Looker\Value\Doctype;

use function array_filter;
use function array_map;
use function array_unshift;
use function array_values;
use function implode;
use function sprintf;

final class HeadMeta implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $meta = [];
    private string $separator;

    public function __construct(
        private readonly Doctype $doctype,
        private readonly HtmlAttributes $attributePlugin,
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
        return new Tag('meta', AttributeNormaliser::normalise(
            $attributes,
            new MetaAttribute(),
        ), null);
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

    private function tagToString(Tag $tag): string
    {
        $attributeString = ($this->attributePlugin)($tag->attributes);

        if ($attributeString === '') {
            return '';
        }

        return sprintf(
            '<%s %s%s>',
            $tag->name,
            $attributeString,
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
