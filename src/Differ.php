#!/usr/bin/env php
<?php

namespace src\Differ\genDiff;

use Symfony\Component\Yaml\Yaml;
use SplFileInfo;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $jsonArray1 = extension($pathToFile1);
    $jsonArray2 = extension($pathToFile2);

    print_r($jsonArray1);
    print_r($jsonArray2);

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

function extension(string $nameFile)
{
    $info = new SplFileInfo($nameFile);
    $extension = $info->getExtension();

    if ($extension === 'json') {
        return json_decode(file_get_contents($nameFile), true);
    } elseif ($extension === 'yaml' || $extension === 'yml') {
        return json_decode(json_encode(Yaml::parseFile($nameFile, Yaml::PARSE_OBJECT_FOR_MAP)), true);
    }
}
