<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function \src\Differ\genDiff\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $this->assertSame(
            file_get_contents(__DIR__ . "/fixtures/result.txt"),
            genDiff(__DIR__ . "/fixtures/file1.json", __DIR__ . "/fixtures/file2.json")
        );
    }
}
