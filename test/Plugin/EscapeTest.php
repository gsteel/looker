<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Escape;
use PHPUnit\Framework\TestCase;

class EscapeTest extends TestCase
{
    public function testInvokeAcceptsAStringAndEscapesHtmlByDefault(): void
    {
        $input = '<html>&</html>';

        $plugin = new Escape(new Escaper());

        $expect = '&lt;html&gt;&amp;&lt;/html&gt;';

        self::assertSame($expect, $plugin($input));

        self::assertNotSame($expect, $plugin->css($input));
        self::assertNotSame($expect, $plugin->js($input));
        self::assertNotSame($expect, $plugin->url($input));
        self::assertNotSame($expect, $plugin->attribute($input));
    }

    public function testEscapeCss(): void
    {
        $plugin = new Escape(new Escaper());
        self::assertSame(
            '\3C b\3E bar\3C \2F b\3E ',
            $plugin->css('<b>bar</b>'),
        );
    }

    public function testEscapeJs(): void
    {
        $plugin = new Escape(new Escaper());
        self::assertSame(
            '\x3Cb\x3Ebar\x3C\x2Fb\x3E',
            $plugin->js('<b>bar</b>'),
        );
    }

    public function testEscapeUrl(): void
    {
        $plugin = new Escape(new Escaper());
        self::assertSame(
            '%3Cb%3Ebar%3C%2Fb%3E',
            $plugin->url('<b>bar</b>'),
        );
    }

    public function testEscapeAttribute(): void
    {
        $plugin = new Escape(new Escaper());
        self::assertSame(
            '&lt;b&gt;bar&lt;&#x2F;b&gt;',
            $plugin->attribute('<b>bar</b>'),
        );
    }

    public function testEscapeHtml(): void
    {
        $plugin = new Escape(new Escaper());
        self::assertSame(
            '&lt;b&gt;bar&lt;/b&gt;',
            $plugin->html('<b>bar</b>'),
        );
    }
}
