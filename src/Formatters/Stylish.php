<?php

namespace Differ\Formatters\Stylish;

use function Differ\Tree\getKey;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getOperation;
use function Differ\Tree\isChanged;
use function Differ\Tree\getValue;

use const Differ\Tree\OPERATION_ADDED;
use const Differ\Tree\OPERATION_REMOVED;

function format(array $value): string
{
    $iter = function ($currentValue, $level = 1) use (&$iter) {
        if (is_scalar($currentValue) || $currentValue === null) {
            return toString($currentValue);
        }

        $currentReplacer = getReplacer('  ', 1, $level);

        $lines = array_map(function ($value) use ($level, $iter, $currentReplacer) {
            $key = getKey($value);
            $getValue = getValue($value);
            $oldValue = getOldValue($value);

            if (isChanged($value)) {
                return [
                    "{$currentReplacer}- {$key}: {$iter($oldValue, $level + 2)}",
                    "{$currentReplacer}+ {$key}: {$iter($getValue, $level + 2)}",
                ];
            }

            $sign = getSign(getOperation($value));

            return ["{$currentReplacer}{$sign}{$key}: {$iter($getValue, $level + 2)}"];
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
    if ($operation === OPERATION_REMOVED) {
        return '- ';
    }

    if ($operation === OPERATION_ADDED) {
        return '+ ';
    }

    return '  ';
}

function toString(mixed $node): string
{
    if (is_numeric($node)) {
        return (string)$node;
    }

    if (is_bool($node)) {
        return $node ? 'true' : 'false';
    }

    if ($node === null) {
        return 'null';
    }

    return (string)$node;
}
