<?php

namespace Differ\Tree;

const OPERATION_CHANGED = 'changed';
const OPERATION_ADDED = 'added';
const OPERATION_REMOVED = 'removed';
const OPERATION_NOT_CHANGED = 'not_changed';
const TYPE_SIMPLE = 'simple';
const TYPE_OBJECT = 'object';

function createNode(
    string $key,
    string $type,
    string $operation,
    mixed $value,
    mixed $oldType = null,
    mixed $oldValue = null,
): array {
    return [
        'key' => $key,
        'type' => $type,
        'operation' => $operation,
        'value' => $value,
        'oldType' => $oldType,
        'oldValue' => $oldValue,
    ];
}

function getKey(array $node): string
{
    return $node['key'];
}

function getType(array $node): string
{
    return $node['type'];
}

function isObject(array $node): bool
{
    return getType($node) === TYPE_OBJECT;
}

function isOldObject(array $node): bool
{
    return getOldType($node) === TYPE_OBJECT;
}

function getOperation(array $node): string
{
    return $node['operation'];
}

function isChanged(array $node): bool
{
    return getOperation($node) === OPERATION_CHANGED;
}

function isAdded(array $node): bool
{
    return getOperation($node) === OPERATION_ADDED;
}

function isRemoved(array $node): bool
{
    return getOperation($node) === OPERATION_REMOVED;
}

function getValue(array $node)
{
    return $node['value'];
}

function getOldType(array $node): string
{
    return $node['oldType'];
}

function getOldValue(array $node)
{
    return $node['oldValue'];
}
