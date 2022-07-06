<?php

namespace Differ\Formatters\JSONFormatter;

/**
 * @param array<int, mixed> $astTreeData
 * @return string
 */
function makeFormattedDiff(array $astTreeData): string
{
     return json_encode($astTreeData);
}
