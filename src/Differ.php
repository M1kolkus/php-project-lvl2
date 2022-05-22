#!/usr/bin/env php
<?php

namespace src\Differ\genDiff;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $content1 = file_get_contents($pathToFile1);
    $content2 = file_get_contents($pathToFile2);

    $jsonArray1 = json_decode($content1, true);
    $jsonArray2 = json_decode($content2, true);

    $keys = array_merge($jsonArray1, $jsonArray2);
    ksort($keys);

    $result = array_map(function ($key) use ($jsonArray2, $jsonArray1) {
        return getLine($key, $jsonArray1, $jsonArray2);
    }, array_keys($keys));

    $result = array_merge(...$result);

    $lines = ['{', ...$result, '}'];

    return implode("\n", $lines);
}

function toString($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if ($value === null) {
        return 'null';
    }

    return (string)$value;
}

function getLine(string $key, array $pathToFile1, array $pathToFile2): array
{
    if (array_key_exists($key, $pathToFile1) && array_key_exists($key, $pathToFile2)) {
        if ($pathToFile1[$key] === $pathToFile2[$key]) {
            $value = toString($pathToFile1[$key]);

            return ["   {$key}: {$value}"];
        }

        $value1 = toString($pathToFile1[$key]);
        $value2 = toString($pathToFile2[$key]);

        return [
            "-  {$key}: {$value1}",
            "+  {$key}: {$value2}",
        ];
    }

    if (array_key_exists($key, $pathToFile1)) {
        $value = toString($pathToFile1[$key]);

        return ["-  {$key}: {$value}"];
    }

    $value = toString($pathToFile2[$key]);

    return ["+  {$key}: {$value}"];
}
