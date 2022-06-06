<?php

namespace src\Formatters;

use src\Formatters\Stylish;
use src\Formatters\Plain;

function format(array $data, string $format): string
{
    if ($format === 'stylish') {
        return Stylish\format($data);
    }

    return Plain\format($data);
}
