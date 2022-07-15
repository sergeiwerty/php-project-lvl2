<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

/**
 * @param string $fileName
 * @return array<int, string|false>
 */
function getFileContent(string $fileName): array
{

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

    return $mapping[$type]($content);
}
