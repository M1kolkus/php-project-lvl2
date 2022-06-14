<?php

namespace Differ\Differ;

use SplFileInfo;
use function Functional\sort;
use function Differ\Parsers\getParsers;
use function Differ\Formatters\format;
use const Differ\Formatters\FORMAT_STYLISH;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = FORMAT_STYLISH): string
{
    $info = new SplFileInfo($pathToFile1);
    $extension = $info->getExtension();

    $arr1 = parsers($extension, $pathToFile1);
    $arr2 = parsers($extension, $pathToFile2);

    $buildDiff = buildDiff($arr1, $arr2);

    $format = format($formatName);

    return $format($buildDiff);
}

function parsers(string $extension, string $pathToFile): array
{
    $getParsers = getParsers($extension);

    if ($extension === 'yml' || $extension === 'yaml') {
        return $getParsers($pathToFile);
    }
    return $getParsers(file_get_contents($pathToFile), true);
}

function buildDiff(array $arr1, array $arr2): array
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    $newKeys = array_unique($keys);
    $sortedKeys = sort($newKeys, fn($key1, $key2) => $key1 <=> $key2);

    return array_reduce($sortedKeys, function (array $acc, string $key) use ($arr1, $arr2) {
        $existsInFirstArray = array_key_exists($key, $arr1);
        $existsInSecondArray = array_key_exists($key, $arr2);

        $value = array_key_exists($key, $arr2) ? $arr2[$key] : $arr1[$key];

        if (is_array($value)) {
            $type = 'object';
            $newValue = buildDiff($value, $value);
        } else {
            $type = 'simple';
        }

        if ($existsInFirstArray && $existsInSecondArray) {
            if (is_array($arr1[$key]) && is_array($arr2[$key])) {
                $operation = 'not_changed';
                $valueChildren = buildDiff($arr1[$key], $arr2[$key]);
            } elseif ($arr1[$key] === $arr2[$key]) {
                $operation = 'not_changed';
            } else {
                $operation = 'changed';
                $oldValue = is_array($arr1[$key]) ? buildDiff($arr1[$key], $arr1[$key]) : $arr1[$key];

                if (is_array($arr1[$key]) !== is_array($arr2[$key])) {
                    $oldType = is_array($arr1[$key]) ? 'object' : 'simple';
                }
            }
        } elseif ($existsInFirstArray) {
            $operation = 'disappeared';
        } else {
            $operation = 'appeared';
        }

        $node = [
            'key' => $key,
            'type' => $type,
            'oldType' => $oldType ?? null,
            'operation' => $operation,
            'value' => $valueChildren ?? $newValue ?? $value,
            'oldValue' => $oldValue ?? null,
        ];

        return [...$acc, $node];
    }, []);
}
