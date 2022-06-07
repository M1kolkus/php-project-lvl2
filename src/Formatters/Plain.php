<?php

namespace src\Formatters\Plain;

function format(array $value): string
{
    return toPlain($value, '');
}

function toPlain(array $value, string $parents): string
{
    $lines = array_map(function ($node) use ($parents) {
        $path = "{$parents}{$node['key']}";

        if ($node['type'] === 'object') {
            $stringNewValue = '[complex value]';
        } else {
            $stringNewValue = string($node['value']);
        }

        if ($node['operation'] === 'appeared') {
            return "Property '{$path}' was added with value: {$stringNewValue}";
        }

        if ($node['operation'] === 'disappeared') {
            return "Property '{$path}' was removed";
        }

        if ($node['operation'] === 'changed') {
            if (array_key_exists('oldType', $node) && $node['oldType'] === 'object') {
                $stringOldValue = '[complex value]';
            } else {
                $stringOldValue = string($node['oldValue']);
            }

            return "Property '{$path}' was updated. From {$stringOldValue} to {$stringNewValue}";
        }

        if ($node['type'] === 'object') {
            return toPlain($node['value'], "{$path}.");
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
