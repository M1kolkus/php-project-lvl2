<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getKey;
use function Differ\Tree\getOldValue;
use function Differ\Tree\isChanged;
use function Differ\Tree\isObject;
use function Differ\Tree\isOldObject;
use function Differ\Tree\isAdded;
use function Differ\Tree\isRemoved;
use function Differ\Tree\getValue;

function format(array $value): string
{
    return toPlain($value, '');
}

function toPlain(array $value, string $parents): string
{
    $lines = array_map(function ($node) use ($parents) {
        $key = getKey($node);
        $path = "{$parents}{$key}";
        $stringNewValue = isObject($node) ? '[complex value]' : string(getValue($node));

        if (isAdded($node)) {
            return "Property '{$path}' was added with value: {$stringNewValue}";
        }

        if (isRemoved($node)) {
            return "Property '{$path}' was removed";
        }

        if (isChanged($node)) {
            $stringOldValue = isOldObject($node) ? '[complex value]' : string(getOldValue($node));

            return "Property '{$path}' was updated. From {$stringOldValue} to {$stringNewValue}";
        }

        if (isObject($node)) {
            return toPlain(getValue($node), "{$path}.");
        }

        return null;
    }, $value);

    return implode("\n", array_filter($lines));
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
