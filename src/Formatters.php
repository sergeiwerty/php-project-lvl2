<?php

namespace Differ\Formatters;

function getFormattedDiff ($astTreeData, $format): string
{
    switch ($format) {
        case 'stylish':
            return stylishFormatter\makeFormattedDiff($astTreeData);
        case 'plain':
            return plainFormatter\makeFormattedDiff($astTreeData);
        case 'json':
            return JSONFormatter\makeFormattedDiff($astTreeData);
    }
}
//$formattersTree =
//    'stylish' => fn(),
//    'plain' => '',
//    'json'=> ''
//];
