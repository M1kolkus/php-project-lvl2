<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getParser(string $extension): callable
{
    return match (strtolower($extension)) {
        'yaml', 'yml' => fn(string $content) => Yaml::parseFile($content),
        default => fn(string $content) => json_decode($content, true),
    };
}
