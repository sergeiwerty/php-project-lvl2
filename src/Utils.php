<?php

namespace Differ\Utils;

function getFixtureFullPath($fixtureName): string
{
//    var_dump($fixtureName);
    $parts = [__DIR__, '../tests/fixtures', $fixtureName];
//    var_dump(implode('/', $parts));
//    var_dump(realpath(implode('/', $parts)));
    return realpath(implode('/', $parts));
}
