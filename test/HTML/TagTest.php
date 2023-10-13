<?php

declare(strict_types=1);

namespace Looker\Test\HTML;

use Looker\HTML\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testEquality(): void
    {
        $a = new Tag('link', [
            'rel' => 'stylesheet',
            'href' => '/assets/styles.css',
        ], null);

        $b = new Tag('link', [
            'href' => '/assets/styles.css',
            'rel' => 'stylesheet',
        ], null);

        $c = new Tag('link', [
            'href' => '/assets/other-styles.css',
            'rel' => 'stylesheet',
        ], null);

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }
}
