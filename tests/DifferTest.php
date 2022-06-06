<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function \src\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testStylishGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/resultStylish.txt"),
            genDiff(__DIR__ . "/fixtures/file1.json", __DIR__ . "/fixtures/file2.json")
        );

        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/resultStylish.txt"),
            genDiff(__DIR__ . "/fixtures/filepath1.yml", __DIR__ . "/fixtures/filepath2.yml")
        );
    }

    public function testPlainGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/resultPlain.txt"),
            genDiff(__DIR__ . "/fixtures/file1.json", __DIR__ . "/fixtures/file2.json")
        );

        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/resultPlain.txt"),
            genDiff(__DIR__ . "/fixtures/filepath1.yml", __DIR__ . "/fixtures/filepath2.yml")
        );
    }
}



