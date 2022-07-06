<?php

namespace Differ\Formatters;

use Exception;

/**
 * @param array<int, mixed> $astTreeData
 * @param string $format
 * @return string
 * @throws Exception
 */
function getFormattedDiff(array $astTreeData, string $format): string
{
    switch ($format) {
        case 'stylish':
            return stylishFormatter\makeFormattedDiff($astTreeData);
        case 'plain':
            return plainFormatter\makeFormattedDiff($astTreeData);
        case 'json':
            return JSONFormatter\makeFormattedDiff($astTreeData);
        default:
            throw new Exception("Unknown format: {$format}!");
    }
}
