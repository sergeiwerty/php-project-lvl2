<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Utils\getFixtureFullPath;
use function Differ\Parsers\Parser\parse;

function genDiff(string $firstPath, string $secondPath, string $style = ''): string
{
//    var_dump(__DIR__);
//    var_dump($firstPath);
//    var_dump($secondPath);

//    $pathToFile1 = getFixtureFullPath($firstPath);
//    $pathToFile2 = getFixtureFullPath($secondPath);
//
//    $fileContent1 = file_get_contents($pathToFile1);
//    $fileContent2 = file_get_contents($pathToFile2);
//    $jsonToArr1 = json_decode($fileContent1, true);
//    $jsonToArr2 = json_decode($fileContent2, true);

    $parsedFile1 = parse($firstPath);
    $parsedFile2 = parse($secondPath);

    var_dump($parsedFile1);
    var_dump(trim(var_export($parsedFile1, true), "'"));

    $merged = array_keys(array_merge($parsedFile1, $parsedFile2));

    $sortedKeys = sort($merged, fn ($left, $right) => strcmp($left, $right));

    $resultString = "{\n";

    foreach ($sortedKeys as $key) {
        if (!array_key_exists($key, $parsedFile1)) {
            $resultString .= "\t" . '+ ' . $key . ': ' . (var_export($parsedFile2[$key], true)) . "\n";
        } elseif (!array_key_exists($key, $parsedFile2)) {
            $resultString .= "\t" . '- ' . $key . ': ' . (var_export($parsedFile1[$key], true)) . "\n";
        } elseif ($parsedFile1[$key] !== $parsedFile2[$key]) {
            $resultString .= "\t" . '- ' . $key . ': ' . (var_export($parsedFile1[$key], true)) . "\n";
            $resultString .= "\t" . '+ ' . $key . ': ' . (var_export($parsedFile2[$key], true)) . "\n";
        } else {
            $resultString .= "\t" . '  ' . $key . ': ' . (var_export($parsedFile1[$key], true)) . "\n";
        }
    }
    $resultString .= '}';

    return $resultString;
}
