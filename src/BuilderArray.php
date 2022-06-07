<?php

namespace src\BuilderArray;

use function Functional\sort;

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
            $value = buildDiff($value, $value);
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
            'value' => $valueChildren ?? $value,
            'oldValue' => $oldValue ?? null,
        ];

        return [...$acc, $node];
    }, []);
}
