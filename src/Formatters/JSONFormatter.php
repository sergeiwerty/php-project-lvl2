<?php

namespace Differ\Formatters\JSONFormatter;

function makeFormattedDiff(array $astTreeData): string
{
     return json_encode($astTreeData);
}
