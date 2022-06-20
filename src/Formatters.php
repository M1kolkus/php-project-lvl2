<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;

const FORMAT_STYLISH = 'stylish';
const FORMAT_PLAIN = 'plain';
const FORMAT_JSON = 'json';

function getFormatter(string $format): callable
{
    return match ($format) {
        FORMAT_PLAIN => fn(array $buildDiff) => Plain\format($buildDiff),
        FORMAT_JSON => fn(array $buildDiff) => json_encode($buildDiff),
        default => fn(array $buildDiff) => Stylish\format($buildDiff),
    };
}
