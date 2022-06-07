<?php

namespace src\Formatters\Stylish;

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
                    "{$currentReplacer}- {$value['key']}: {$iter($value['oldValue'], $level + 2)}",
                    "{$currentReplacer}+ {$value['key']}: {$iter($value['value'], $level + 2)}",
                ];
            }

            return ["{$currentReplacer}{$sign}{$value['key']}: {$iter($value['value'], $level + 2)}"];
        },
            $currentValue);

        $lines = array_merge(...$lines);
        $lines = ['{', ...$lines, getReplacer('  ', 1, $level - 1) . '}'];

        return implode("\n", $lines);
    };

    return $iter($value);
}

function getReplacer(string $replacer, int $spacesCount, int $level): string
{
    return str_repeat($replacer, $spacesCount * $level);
}
