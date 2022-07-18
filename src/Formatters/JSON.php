<?php

namespace Differ\Formatters\JSON;

/**
 * @param array<int, mixed> $astTreeData
 * @return string|false
 */
function makeFormattedDiff(array $astTreeData): string|false
{
     return json_encode($astTreeData);
}
