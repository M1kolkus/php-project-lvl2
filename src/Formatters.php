<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;

const FORMAT_STYLISH = 'stylish';
const FORMAT_PLAIN = 'plain';
const FORMAT_JSON = 'json';

function format(string $format): callable
{
    if ($format === FORMAT_PLAIN) {
        return Plain\format(...);
    }

    if ($format === FORMAT_JSON) {
        return json_encode(...);
    }

    return Stylish\format(...);
}
