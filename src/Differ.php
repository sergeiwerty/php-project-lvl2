<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Builder\buildDiff;
use function Differ\Formatters\getFormattedDiff;

function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string|false
{
    $firstData = parse($firstPath);
    $secondData = parse($secondPath);

    $diffOfFiles = buildDiff($firstData, $secondData);

    return getFormattedDiff($diffOfFiles, $format);
}
