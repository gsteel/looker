<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\LinkAttribute;
use Looker\HTML\Tag;
use Looker\Value\Doctype;

use function array_change_key_case;
use function array_map;
use function array_unshift;
use function array_values;
use function implode;
use function sprintf;

use const CASE_LOWER;

final class HeadLink implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $links = [];
    private string $separator;

    public function __construct(
        private readonly Doctype $doctype,
        private readonly HtmlAttributes $attributeHelper,
        private readonly string $defaultSeparator = "\n\t",
    ) {
        $this->separator = $this->defaultSeparator;
    }

    public function resetState(): void
    {
        $this->links = [];
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
     * @param non-empty-string $rel
     * @param non-empty-string $href
     * @param array<non-empty-string, scalar> $attributes
     */
    public function append(string $rel, string $href, array $attributes = []): self
    {
        $tag = $this->makeTag($rel, $href, $attributes);
        $this->remove($tag);
        $this->links[] = $tag;

        return $this;
    }

    /**
     * @param non-empty-string $rel
     * @param non-empty-string $href
     * @param array<non-empty-string, scalar> $attributes
     */
    public function prepend(string $rel, string $href, array $attributes = []): self
    {
        $tag = $this->makeTag($rel, $href, $attributes);
        $this->remove($tag);
        array_unshift($this->links, $tag);

        return $this;
    }

    /**
     * @param non-empty-string $rel
     * @param non-empty-string $href
     * @param array<non-empty-string, scalar> $attributes
     */
    private function makeTag(string $rel, string $href, array $attributes): Tag
    {
        $attributes = array_change_key_case($attributes, CASE_LOWER);
        $attributes['rel'] = $rel;
        $attributes['href'] = $href;

        /** @psalm-var array<non-empty-lowercase-string, scalar> $attributes */

        return new Tag('link', $attributes, null);
    }

    public function removeWithLink(string $href): self
    {
        foreach ($this->links as $link) {
            if (($link->attributes['href'] ?? null) !== $href) {
                continue;
            }

            $this->remove($link);
            break;
        }

        return $this;
    }

    private function remove(Tag $tag): void
    {
        $links = $this->links;

        foreach ($links as $key => $link) {
            if (! $link->equals($tag)) {
                continue;
            }

            unset($links[$key]);
            break;
        }

        $this->links = array_values($links);
    }

    private function tagToString(Tag $tag): string
    {
        $attributes = AttributeNormaliser::normalise($tag->attributes, new LinkAttribute());

        return sprintf(
            '<%s %s%s>',
            $tag->name,
            ($this->attributeHelper)($attributes),
            $this->doctype->isXhtml() ? ' /' : '',
        );
    }

    public function toString(): string
    {
        $links = array_map(
            fn (Tag $tag): string => $this->tagToString($tag),
            $this->links,
        );

        return implode($this->separator, $links);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
