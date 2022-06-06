<?php

namespace src\Formatters;

use src\Formatters\Stylish;
use src\Formatters\Plain;

function format(array $buildDiff, string $format): string
{
    if ($format === 'stylish') {
        return Stylish\format($buildDiff);
    }

    if ($format === 'plain') {
        return Plain\format($buildDiff);
    }

    if ($format === 'json') {
        return  json_encode($buildDiff);
    }
}
