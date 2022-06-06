#!/usr/bin/env php
<?php

namespace src\Differ;

use function src\Parsers\parsers;
use function src\BuilderArray\buildDiff;
use function src\Formatters\format;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish'): string
{
    $arr1 = parsers($pathToFile1);
    $arr2 = parsers($pathToFile2);

    $buildDiff = buildDiff($arr1, $arr2);

    return format($buildDiff, $formatName);
}

