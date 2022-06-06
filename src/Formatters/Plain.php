<?php

namespace src\Formatters\Plain;

function format($value): string
{
    return toPlain($value, '');
}

function toPlain($value, $parents): string
{
    $lines = array_map(function ($node) use ($parents) {
        $stringNewValue = string($node['value']);
        $path = "{$parents}{$node['key']}";

        if ($node['type'] === 'object') {
            $stringNewValue = '[complex value]';
        }

        if ($node['operation'] === 'appeared') {
            return "Property '{$path}' was added with value: {$stringNewValue}";
        }

        if ($node['operation'] === 'disappeared') {
            return "Property '{$path}' was removed";
        }

        if ($node['operation'] === 'changed') {
            $stringOldValue = string($node['oldValue']);

            if (array_key_exists('oldType', $node) &&
                $node['oldType'] === 'object') {
                $stringOldValue = '[complex value]';
            }

            return "Property '{$path}' was updated. From {$stringOldValue} to {$stringNewValue}";
        }

        if ($node['type'] === 'object') {
            return toPlain($node['value'], "{$path}.");
        }

        if ($node['operation'] === 'not_changed') {
            return null;
        }
    }, $value);

    return implode("\n", array_filter($lines, fn ($value) => !is_null($value)));
}

function string($node): string
{
    if (is_numeric($node)) {
        return (string)$node;
    }

    if (is_string($node)) {
        return '\'' . (string)$node . '\'';
    }

    if (is_bool($node)) {
        return $node ? 'true' : 'false';
    }

    if ($node === null) {
        return 'null';
    }
}
