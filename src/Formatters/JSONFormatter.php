<?php

namespace Differ\Formatters\JSONFormatter;

/**
 * @param array<int, mixed> $astTreeData
 * @return string|false
 */
function makeFormattedDiff(array $astTreeData): string|false
{
     return json_encode($astTreeData);
}
