<?php

declare(strict_types=1);

namespace Looker\Test\Plugin;

use Laminas\Escaper\Escaper;
use Looker\Plugin\Javascript;
use PHPUnit\Framework\TestCase;

class JavascriptTest extends TestCase
{
    private Javascript $plugin;

    protected function setUp(): void
    {
        $this->plugin = new Javascript(new Escaper(), "\n");
    }

    public function testThatFilesCanBeAppended(): void
    {
        $this->plugin->appendFile('one.js')
            ->appendFile('two.js');

        $expect = <<<'HTML'
            <script src="one.js"></script>
            <script src="two.js"></script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testPrependFile(): void
    {
        $this->plugin->appendFile('one.js')
            ->prependFile('two.js');

        $expect = <<<'HTML'
            <script src="two.js"></script>
            <script src="one.js"></script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testSeparatorCanBeChanged(): void
    {
        $this->plugin->appendFile('one.js')
            ->appendFile('two.js')
            ->setSeparator('~');

        $expect = <<<'HTML'
            <script src="one.js"></script>~<script src="two.js"></script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatAddingTheSameFileTwiceIsANoOp(): void
    {
        $this->plugin->appendFile('one.js')
            ->appendFile('one.js');

        $expect = <<<'HTML'
            <script src="one.js"></script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testInvokeReturnsSelf(): void
    {
        self::assertSame($this->plugin, $this->plugin->__invoke());
    }

    public function testThatClearingStateRemovesExistingTags(): void
    {
        $this->plugin->appendFile('one.js')
            ->resetState();

        self::assertSame('', $this->plugin->toString());
    }

    public function testThatAttributesCanBeAddedToFiles(): void
    {
        $this->plugin->appendFile('one.js', ['defer' => true, 'fetchpriority' => 'high']);

        self::assertSame(
            '<script defer fetchpriority="high" src="one.js"></script>',
            $this->plugin->toString(),
        );
    }

    public function testThatFalseBooleanAttributesAreOmitted(): void
    {
        $this->plugin->appendFile('one.js', ['defer' => false, 'fetchpriority' => 'high']);

        self::assertSame(
            '<script fetchpriority="high" src="one.js"></script>',
            $this->plugin->toString(),
        );
    }

    public function testThatUnknownAttributesAreOmitted(): void
    {
        $this->plugin->appendFile('one.js', ['goats' => 'are great']);

        self::assertSame(
            '<script src="one.js"></script>',
            $this->plugin->toString(),
        );
    }

    public function testThatFilesCanBeRemovedByMatchingTheSrcAttribute(): void
    {
        $this->plugin->appendFile('one.js')
            ->appendFile('two.js')
            ->removeFile('two.js');

        $expect = <<<'HTML'
            <script src="one.js"></script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatInlineScriptsCanBeAppendedAndPrepended(): void
    {
        $this->plugin->appendScript('alert("One");')
            ->prependScript('alert("Two");');

        $expect = <<<'HTML'
            <script>
            alert("Two");
            </script>
            <script>
            alert("One");
            </script>
            HTML;

        self::assertSame($expect, $this->plugin->toString());
    }

    public function testThatCompletelyEmptyScriptsAreIgnored(): void
    {
        /** @psalm-suppress InvalidArgument */
        $this->plugin->appendScript('');
        self::assertSame('', $this->plugin->toString());
    }

    public function testThatThePluginCanBeCastToAString(): void
    {
        $this->plugin->appendFile('one.js');

        $expect = <<<'HTML'
            <script src="one.js"></script>
            HTML;

        self::assertSame($expect, (string) $this->plugin);
    }
}
