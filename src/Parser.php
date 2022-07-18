<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

/**
 * @param array<int, string|false> $rawData
 * @return array<int, mixed>
 */
function parse(array $rawData): array
{
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
