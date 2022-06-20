<?php

namespace Differ\Differ;

use SplFileInfo;

use function Differ\Tree\createNode;
use function Differ\Parsers\getParser;
use function Differ\Formatters\format;
use function Functional\sort;

use const Differ\Formatters\FORMAT_STYLISH;
use const Differ\Tree\OPERATION_ADDED;
use const Differ\Tree\OPERATION_CHANGED;
use const Differ\Tree\OPERATION_NOT_CHANGED;
use const Differ\Tree\OPERATION_REMOVED;
use const Differ\Tree\TYPE_OBJECT;
use const Differ\Tree\TYPE_SIMPLE;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = FORMAT_STYLISH): string
{
    $component1 = getContent($pathToFile1);
    $component2 = getContent($pathToFile2);

    $buildDiff = buildDiff($component1, $component2);

    $format = format($formatName);

    return $format($buildDiff);
}

function getContent(string $pathToFile): array
{
    $info = new SplFileInfo($pathToFile);
    $extension = $info->getExtension();
    $parser = getParser($extension);
    $content = file_get_contents($pathToFile);

    return $parser($content);
}

function buildDiff(array $arr1, array $arr2): array
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    $newKeys = array_unique($keys);
    $sortedKeys = sort($newKeys, fn($key1, $key2) => $key1 <=> $key2);

    return array_reduce($sortedKeys, function (array $acc, string $key) use ($arr1, $arr2) {
        $existsInFirstArray = array_key_exists($key, $arr1);
        $existsInSecondArray = array_key_exists($key, $arr2);
        $firstIsObject = $existsInFirstArray && is_array($arr1[$key]);
        $secondIsObject = $existsInSecondArray && is_array($arr2[$key]);

        if ($firstIsObject && $secondIsObject) {
            $node = createNode($key, TYPE_OBJECT, OPERATION_NOT_CHANGED, buildDiff($arr1[$key], $arr2[$key]));

            return [...$acc, $node];
        }

        if ($existsInFirstArray && $existsInSecondArray && $arr1[$key] !== $arr2[$key]) {
            $value1 = $firstIsObject ? buildDiff($arr1[$key], $arr1[$key]) : $arr1[$key];
            $value2 = $secondIsObject ? buildDiff($arr2[$key], $arr2[$key]) : $arr2[$key];

            $node = createNode(
                $key,
                $secondIsObject ? TYPE_OBJECT : TYPE_SIMPLE,
                OPERATION_CHANGED,
                $value2,
                $firstIsObject ? TYPE_OBJECT : TYPE_SIMPLE,
                $value1,
            );

            return [...$acc, $node];
        }

        if ($existsInFirstArray && $existsInSecondArray) {
            $node = createNode($key, TYPE_SIMPLE, OPERATION_NOT_CHANGED, $arr1[$key]);

            return [...$acc, $node];
        }

        if ($existsInFirstArray) {
            $value = $firstIsObject ? buildDiff($arr1[$key], $arr1[$key]) : $arr1[$key];
            $node = createNode($key, $firstIsObject ? TYPE_OBJECT : TYPE_SIMPLE, OPERATION_REMOVED, $value);

            return [...$acc, $node];
        }

        $value = $secondIsObject ? buildDiff($arr2[$key], $arr2[$key]) : $arr2[$key];
        $node = createNode($key, $secondIsObject ? TYPE_OBJECT : TYPE_SIMPLE, OPERATION_ADDED, $value);

        return [...$acc, $node];
    }, []);
}
