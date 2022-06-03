<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function \src\Differ\genDiff\genDiff;

class DifferTest extends TestCase
{
    public function testFlatGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/test1/result.txt"),
            genDiff(__DIR__ . "/fixtures/test1/file1.json", __DIR__ . "/fixtures/test1/file2.json")
        );

        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/test1/result.txt"),
            genDiff(__DIR__ . "/fixtures/test1/filepath1.yml", __DIR__ . "/fixtures/test1/filepath2.yml")
        );
    }

    public function testArrayGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/test2/result.txt"),
            genDiff(__DIR__ . "/fixtures/test2/file1.json", __DIR__ . "/fixtures/test2/file2.json")
        );

        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/test2/result.txt"),
            genDiff(__DIR__ . "/fixtures/test2/filepath1.yml", __DIR__ . "/fixtures/test2/filepath2.yml")
        );
    }
}
