<?php

namespace src\Parsers;

use Symfony\Component\Yaml\Yaml;
use SplFileInfo;

function parsers(string $nameFile): array
{
    $info = new SplFileInfo($nameFile);
    $extension = $info->getExtension();

    if ($extension === 'yaml' || $extension === 'yml') {
        return Yaml::parseFile($nameFile);
    }

    return json_decode(file_get_contents($nameFile), true);
}
