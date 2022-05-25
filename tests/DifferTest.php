<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function \src\Differ\genDiff\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $this->assertSame(
            file_get_contents("/home/alexander/domains/php-project-lvl2/tests/fixtures/result.txt"),
            genDiff(
                "/home/alexander/domains/php-project-lvl2/tests/fixtures/file1.json",
                "/home/alexander/domains/php-project-lvl2/tests/fixtures/file2.json"
            )
        );
    }
}
