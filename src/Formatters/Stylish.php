<?php

namespace Differ\Formatters\Stylish;

function format(array $value): string
{
    $iter = function ($currentValue, $level = 1) use (&$iter) {
        if (is_string($currentValue) || is_numeric($currentValue)) {
            return (string)$currentValue;
        }

        if (is_bool($currentValue)) {
            return $currentValue ? 'true' : 'false';
        }

        if ($currentValue === null) {
            return 'null';
        }

        $currentReplacer = getReplacer('  ', 1, $level);

        $lines = array_map(function ($value) use ($level, $iter, $currentReplacer) {
            if ($value['operation'] === 'changed') {
                return [
                    "{$currentReplacer}- {$value['key']}: {$iter($value['oldValue'], $level + 2)}",
                    "{$currentReplacer}+ {$value['key']}: {$iter($value['value'], $level + 2)}",
                ];
            }

            $sign = getSign($value['operation']);

            return ["{$currentReplacer}{$sign}{$value['key']}: {$iter($value['value'], $level + 2)}"];
        }, $currentValue);

        $mergedLines = array_merge(...$lines);
        $result = ['{', ...$mergedLines, getReplacer('  ', 1, $level - 1) . '}'];

        return implode("\n", $result);
    };

    return $iter($value);
}

function getReplacer(string $replacer, int $spacesCount, int $level): string
{
    return str_repeat($replacer, $spacesCount * $level);
}

function getSign(string $operation): string
{
    if ($operation === 'not_changed') {
        return '  ';
    }

    if ($operation === 'disappeared') {
        return '- ';
    }

    if ($operation === 'appeared') {
        return '+ ';
    }

    return '';
}
