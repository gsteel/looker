<?php

declare(strict_types=1);

namespace Looker\Plugin;

use Laminas\Escaper\Escaper;
use Looker\HTML\ScriptAttribute;
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

final class Javascript implements StatefulPlugin
{
    /** @var list<Tag> */
    private array $scripts = [];
    private string $separator;

    public function __construct(
        private readonly Escaper $escaper,
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
        return new Tag('script', $this->normaliseAttributes($attributes), $script);
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
            if (ScriptAttribute::isBoolean($name)) {
                if ($value === false) {
                    continue;
                }

                $result[$name] = true;
                continue;
            }

            if (! ScriptAttribute::exists($name)) {
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
            $this->scripts,
        )));
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
