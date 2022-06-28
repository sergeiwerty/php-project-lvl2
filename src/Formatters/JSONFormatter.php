<?php

namespace Differ\Formatters\JSONFormatter;

function makeFormattedDiff ($astTreeData): string
{
     return json_encode($astTreeData);
};
