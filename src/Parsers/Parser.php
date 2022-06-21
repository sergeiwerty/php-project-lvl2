<?php

namespace Differ\Parsers\Parser;

use Symfony\Component\Yaml\Yaml;

function getFileContent($fileName): array
{
//    var_dump($fileName);
    $parts = [__DIR__, '../../tests/fixtures', $fileName];
    $absolutePath = realpath(implode('/', $parts));

//    $pathToFile = getFullPath($fileName);
    $fileContent = file_get_contents($absolutePath);
    $fileType = pathinfo($absolutePath, PATHINFO_EXTENSION);
    return [$fileContent, $fileType];
}

// Нужно получить type
// type - это то, что указано в расширении файла

//$rawData = getFileContent($fileName);

function parse($fileName) {
    $rawData = getFileContent($fileName);
    [$content, $type] = $rawData;

    $mapping = [
        'yml' =>
            fn($rawData) => Yaml::parse($rawData),
        'yaml' =>
            fn($rawData) => Yaml::parse($rawData),
        'json' =>
            fn($rawData) => json_decode($rawData, true)
    ];

    return $mapping[$type]($content);
}
