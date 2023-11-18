<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Looker\HTML\AttributeNormaliser;
use Looker\HTML\ScriptAttribute;
use Looker\HTML\Tag;

use function array_filter;
use function array_map;
use function array_unshift;
use function array_values;
use function implode;
use function sprintf;

final class Javascript implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $scripts = [];
    private string $separator;

    public function __construct(
        private readonly HtmlAttributes $attributePlugin,
        private readonly string $defaultSeparator = "\n\t",
    ) {
        $this->separator = $this->defaultSeparator;
    }

    public function resetState(): void
    {
        $this->scripts = [];
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
    public function appendFile(string $src, array $attributes = []): self
    {
        $attributes['src'] = $src;
        $tag = $this->makeTag(null, $attributes);
        $this->remove($tag);
        $this->scripts[] = $tag;

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    public function prependFile(string $src, array $attributes = []): self
    {
        $attributes['src'] = $src;
        $tag = $this->makeTag(null, $attributes);
        $this->remove($tag);
        array_unshift($this->scripts, $tag);

        return $this;
    }

    public function removeFile(string $src): self
    {
        foreach ($this->scripts as $key => $tag) {
            $value = $tag->attributes['src'] ?? null;
            if ($value !== $src) {
                continue;
            }

            unset($this->scripts[$key]);
        }

        return $this;
    }

    /**
     * @param non-empty-string $script
     * @param array<non-empty-string, scalar> $attributes
     */
    public function appendScript(string $script, array $attributes = []): self
    {
        $tag = $this->makeTag($script, $attributes);
        $this->remove($tag);
        $this->scripts[] = $tag;

        return $this;
    }

    /**
     * @param non-empty-string $script
     * @param array<non-empty-string, scalar> $attributes
     */
    public function prependScript(string $script, array $attributes = []): self
    {
        $tag = $this->makeTag($script, $attributes);
        $this->remove($tag);
        array_unshift($this->scripts, $tag);

        return $this;
    }

    /** @param array<non-empty-string, scalar> $attributes */
    private function makeTag(string|null $script, array $attributes): Tag
    {
        return new Tag(
            'script',
            AttributeNormaliser::normalise($attributes, new ScriptAttribute()),
            $script,
        );
    }

    private function remove(Tag $tag): void
    {
        $scripts = $this->scripts;

        foreach ($scripts as $key => $item) {
            if (! $item->equals($tag)) {
                continue;
            }

            unset($scripts[$key]);
            break;
        }

        $this->scripts = array_values($scripts);
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
            $attributes === '' ? '' : ' ' . $attributes,
            $content,
            $tag->name,
        );
    }

    public function toString(): string
    {
        return implode($this->separator, array_filter(array_map(
            fn (Tag $tag): string => $this->tagToString($tag),
            $this->scripts,
        )));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
