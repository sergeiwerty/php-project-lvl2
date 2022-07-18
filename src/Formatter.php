<?php

namespace Differ\Formatter;

use Exception;

/**
 * @param array<int, mixed> $astTreeData
 * @param string $format
 * @return string|false
 * @throws Exception
 */
function getFormattedDiff(array $astTreeData, string $format): string|false
{
    switch ($format) {
        case 'stylish':
            return \Differ\Formatters\stylish\makeFormattedDiff($astTreeData);
        case 'plain':
            return \Differ\Formatters\plain\makeFormattedDiff($astTreeData);
        case 'json':
            return \Differ\Formatters\JSON\makeFormattedDiff($astTreeData);
        default:
            throw new Exception("Unknown format: {$format}!");
    }
}
