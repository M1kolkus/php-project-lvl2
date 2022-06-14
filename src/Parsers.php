<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParsers(string $extension): callable
{
    if ($extension === 'yaml' || $extension === 'yml') {
        return Yaml::parseFile(...);
    }

    return json_decode(...);
}
