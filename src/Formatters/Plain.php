<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getKey;
use function Differ\Tree\getOldType;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getOperation;
use function Differ\Tree\getType;
use function Differ\Tree\getValue;

use const Differ\Tree\OPERATION_ADDED;
use const Differ\Tree\OPERATION_CHANGED;
use const Differ\Tree\OPERATION_REMOVED;
use const Differ\Tree\TYPE_OBJECT;

function format(array $value): string
{
    return toPlain($value, '');
}

function toPlain(array $value, string $parents): string
{
    $lines = array_map(function ($node) use ($parents) {
        $key = getKey($node);
        $path = "{$parents}{$key}";
        $stringNewValue = getType($node) === TYPE_OBJECT ? '[complex value]' : string(getValue($node));

        if (getOperation($node) === OPERATION_ADDED) {
            return "Property '{$path}' was added with value: {$stringNewValue}";
        }

        if (getOperation($node) === OPERATION_REMOVED) {
            return "Property '{$path}' was removed";
        }

        if (getOperation($node) === OPERATION_CHANGED) {
            $stringOldValue = getOldType($node) === TYPE_OBJECT ? '[complex value]' : string(getOldValue($node));

            return "Property '{$path}' was updated. From {$stringOldValue} to {$stringNewValue}";
        }

        if (getType($node) === TYPE_OBJECT) {
            return toPlain(getValue($node), "{$path}.");
        }

        return null;
    }, $value);

    return implode("\n", array_filter($lines, fn ($value) => !is_null($value)));
}

function string(mixed $node): string
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

    return '\'' . (string)$node . '\'';
}
