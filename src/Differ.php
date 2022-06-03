#!/usr/bin/env php
<?php

namespace src\Differ\genDiff;

use Symfony\Component\Yaml\Yaml;
use SplFileInfo;

function genDiff(string $pathToFile1, string $pathToFile2)
{
    $arr1 = parsers($pathToFile1);
    $arr2 = parsers($pathToFile2);

    $func = function (array $arr1, array $arr2) use (&$func): array {
        $keys = array_merge(array_keys($arr1), array_keys($arr2));
        $keys = array_unique($keys);
        sort($keys);

        return array_reduce($keys, function (array $acc, string $key) use ($arr1, $arr2, $func) {
            $existsInFirstArray = array_key_exists($key, $arr1);
            $existsInSecondArray = array_key_exists($key, $arr2);

            $node = ['key' => $key];
            $value = array_key_exists($key, $arr2) ? $arr2[$key] : $arr1[$key];

            if (is_array($value)) {
                $node['type'] = 'object';
                $value = $func($value, $value);
            } else {
                $node['type'] = 'simple';
            }

            if ($existsInFirstArray && $existsInSecondArray) {
                if (is_array($arr1[$key]) && is_array($arr2[$key])) {
                    $node['operation'] = 'not_changed';
                    $value = $func($arr1[$key], $arr2[$key]);
                } elseif ($arr1[$key] === $arr2[$key]) {
                    $node['operation'] = 'not_changed';
                } else {
                    $node['operation'] = 'changed';
                    $node['oldValue'] = is_array($arr1[$key]) ? $func($arr1[$key], $arr1[$key]) : $arr1[$key];

                    if (is_array($arr1[$key]) !== is_array($arr2[$key])) {
                        $node['old_type'] = is_array($arr1[$key]) ? 'object' : 'simple';
                    }
                }
            } elseif ($existsInFirstArray) {
                $node['operation'] = 'disappeared';
            } else {
                $node['operation'] = 'appeared';
            }

            $node['value'] = $value;

            return [...$acc, $node];

//            if (!$firstIsObject && !$secondIsObject && $existsInFirstArray && $existsInSecondArray) {
//                if ($arr1[$key] === $arr2[$key]) {
//                    $node = [
//                        'key' => $key,
//                        'type' => 'simple',
//                        'operation' => 'not_changed',
//                        'value' => $arr1[$key],
//                    ];
//                } else {
//                    $node = [
//                        'key' => $key,
//                        'type' => 'simple',
//                        'operation' => 'changed',
//                        'oldValue' => $arr1[$key],
//                        'newValue' => $arr2[$key],
//                    ];
//                }
//
//                return [...$acc, $node];
//            }
//
//            if (!$firstIsObject && !$secondIsObject && $existsInFirstArray && !$existsInSecondArray) {
//                if ($arr2 === []) {
//                    $node = [
//                        'key' => $key,
//                        'type' => 'simple',
//                        'operation' => 'not_changed',
//                        'value' => $arr1[$key],
//                    ];
//                } else {
//                    $node = [
//                    'key' => $key,
//                    'type' => 'simple',
//                    'operation' => 'disappeared',
//                    'value' => $arr1[$key],
//                    ];
//                }
//                return [...$acc, $node];
//            }
//
//
//            if (!$firstIsObject && !$secondIsObject && !$existsInFirstArray && $existsInSecondArray) {
//                $node = [
//                    'key' => $key,
//                    'type' => 'simple',
//                    'operation' => 'appeared',
//                    'value' => $arr2[$key],
//                ];
//
//                return [...$acc, $node];
//            }
//
//            if ($firstIsObject && $secondIsObject) {
//                $node = [
//                    'key' => $key,
//                    'type' => 'object',
//                    'operation' => 'not_changed',
//                    'value' => $func($arr1[$key], $arr2[$key]),
//                ];
//
//                return [...$acc, $node];
//            }
//            if ($firstIsObject && $arr2 === []) {
//                $node = [
//                    'key' => $key,
//                    'type' => 'object',
//                    'operation' => 'not_changed',
//                    'value' => $func($arr1[$key]),
//                ];
//
//
//                return [...$acc, $node];
//            }
//
//            if (!$firstIsObject && $arr2 === []) {
//                $node = [
//                    'key' => $key,
//                    'type' => 'simple',
//                    'operation' => 'not_changed',
//                    'value' => $arr1[$key],
//                ];
//
//
//                return [...$acc, $node];
//            }
//
//            if ($firstIsObject && !$secondIsObject) {
//                if ($existsInSecondArray) {
//                    $node = [
//                        'key' => $key,
//                        'oldType'   => 'object',
//                        'newType'   => 'simple',
//                        'operation' => 'changed',
//                        'oldValue' => $func($arr1[$key]),
//                        'newValue' => $arr2[$key],
//                    ];
//                    return [...$acc, $node];
//                }
//                $node = [
//                    'key' => $key,
//                    'type' => 'object',
//                    'operation' => 'disappeared',
//                    'value' => $func($arr1[$key]),
//                ];
//            } else {
//                $node = [
//                    'key' => $key,
//                    'type' => 'object',
//                    'operation' => 'appeared',
//                    'value' => $func($arr2[$key]),
//                ];
//            }
//            return [...$acc, $node];
        }, []);
    };

    $result = $func($arr1, $arr2);
    return stringify($result);
}

function parsers(string $nameFile): array
{
    $info = new SplFileInfo($nameFile);
    $extension = $info->getExtension();

    if ($extension === 'json') {
        return json_decode(file_get_contents($nameFile), true);
    } elseif ($extension === 'yaml' || $extension === 'yml') {
        return Yaml::parseFile($nameFile);
    }
}

function stringify($value, string $replacer = '  ', int $spacesCount = 1): string
{
    $iter = function ($currentValue, $level = 1) use (&$iter, $replacer, $spacesCount) {
        if (is_string($currentValue) || is_numeric($currentValue)) {
            return (string)$currentValue;
        }

        if (is_bool($currentValue)) {
            return $currentValue ? 'true' : 'false';
        }

        if ($currentValue === null) {
            return 'null';
        }

        $currentReplacer = getReplacer($replacer, $spacesCount, $level);

        $lines = array_map(function ($value) use ($level, $iter, $currentReplacer) {
            $sign = '';
            if ($value['operation'] === 'not_changed') {
                $sign = '  ';
            }

            if ($value['operation'] === 'disappeared') {
                $sign = '- ';
            }

            if ($value['operation'] === 'appeared') {
                $sign = '+ ';
            }

            $spaceValue = $value['value'] === '' ? '' : ' ';

            if ($value['operation'] === 'changed') {
                $spaceOldValue = $value['oldValue'] === '' ? '' : ' ';

                return [
                        "{$currentReplacer}- {$value['key']}:$spaceOldValue{$iter($value['oldValue'], $level + 2)}",
                        "{$currentReplacer}+ {$value['key']}:$spaceValue{$iter($value['value'], $level + 2)}",
                    ];
            }

            return ["{$currentReplacer}{$sign}{$value['key']}:$spaceValue{$iter($value['value'], $level + 2)}"];
        },
            $currentValue);

        $lines = array_merge(...$lines);
        $lines = ['{', ...$lines, getReplacer($replacer, $spacesCount, $level - 1) . '}'];

        return implode("\n", $lines);
    };

    return $iter($value);
}


function getReplacer(string $replacer, int $spacesCount, int $level): string
{
    return str_repeat($replacer, $spacesCount * $level);
}

//var_dump(genDiff(
//    '/home/alexander/domains/php-project-lvl2/tests/fixtures/file1.json',
//    '/home/alexander/domains/php-project-lvl2/tests/fixtures/file2.json',
//));
