<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testStylishGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultStylish.txt'),
            genDiff(__DIR__ . '/fixtures/file1.json', __DIR__ . '/fixtures/file2.json')
        );

        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultStylish.txt'),
            genDiff(__DIR__ . '/fixtures/filepath1.yml', __DIR__ . '/fixtures/filepath2.yml')
        );
    }

    public function testPlainGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultPlain.txt'),
            genDiff(__DIR__ . '/fixtures/file1.json', __DIR__ . '/fixtures/file2.json', 'plain')
        );

        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultPlain.txt'),
            genDiff(__DIR__ . '/fixtures/filepath1.yml', __DIR__ . '/fixtures/filepath2.yml', 'plain')
        );
    }

    public function testJsonGenDiff(): void
    {
        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultJson.txt'),
            genDiff(__DIR__ . '/fixtures/file1.json', __DIR__ . '/fixtures/file2.json', 'json')
        );

        $this->assertSame(
            file_get_contents(__DIR__ . '/fixtures/resultJson.txt'),
            genDiff(__DIR__ . '/fixtures/filepath1.yml', __DIR__ . '/fixtures/filepath2.yml', 'json')
        );
    }
}
