<?php

namespace Differ\Parsers\Parser;

use Symfony\Component\Yaml\Yaml;

/**
 * @param string $fileName
 * @return array
 */
function getFileContent(string $fileName): array
{
//    $parts = [__DIR__, '../../tests/fixtures', $fileName];
//    $absolutePath = realpath(implode('/', $parts));

    if (strpos($fileName, '/') === 0) {
        $parts = [$fileName];
    } else {
        $parts = [__DIR__, '/../', $fileName];
    }
    $absolutePath = implode('', $parts);


    $fileContent = file_get_contents($absolutePath);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    return [$fileContent, $fileType];
}

/**
 * @return array<int, mixed>
 */
function parse(string $fileName): array
{
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
    print_r($mapping[$type]($content));

    return $mapping[$type]($content);
}
