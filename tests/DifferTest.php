<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function \src\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/result.txt"),
            genDiff(__DIR__ . "/fixtures/file1.json", __DIR__ . "/fixtures/file2.json")
        );

        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/result.txt"),
            genDiff(__DIR__ . "/fixtures/filepath1.yml", __DIR__ . "/fixtures/filepath2.yml")
        );
    }
}
