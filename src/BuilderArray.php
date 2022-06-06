<?php

namespace src\BuilderArray;

function buildDiff(array $arr1, array $arr2): array
{
    $keys = array_merge(array_keys($arr1), array_keys($arr2));
    $keys = array_unique($keys);
    sort($keys);

    return array_reduce($keys, function (array $acc, string $key) use ($arr1, $arr2) {
        $existsInFirstArray = array_key_exists($key, $arr1);
        $existsInSecondArray = array_key_exists($key, $arr2);

        $node = ['key' => $key];
        $value = array_key_exists($key, $arr2) ? $arr2[$key] : $arr1[$key];

        if (is_array($value)) {
            $node['type'] = 'object';
            $value = buildDiff($value, $value);
        } else {
            $node['type'] = 'simple';
        }

        if ($existsInFirstArray && $existsInSecondArray) {
            if (is_array($arr1[$key]) && is_array($arr2[$key])) {
                $node['operation'] = 'not_changed';
                $value = buildDiff($arr1[$key], $arr2[$key]);
            } elseif ($arr1[$key] === $arr2[$key]) {
                $node['operation'] = 'not_changed';
            } else {
                $node['operation'] = 'changed';
                $node['oldValue'] = is_array($arr1[$key]) ? buildDiff($arr1[$key], $arr1[$key]) : $arr1[$key];

                if (is_array($arr1[$key]) !== is_array($arr2[$key])) {
                    $node['oldType'] = is_array($arr1[$key]) ? 'object' : 'simple';
                }
            }
        } elseif ($existsInFirstArray) {
            $node['operation'] = 'disappeared';
        } else {
            $node['operation'] = 'appeared';
        }

        $node['value'] = $value;

        return [...$acc, $node];
    }, []);
}
