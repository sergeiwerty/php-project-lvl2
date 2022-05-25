<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Utils\getFixtureFullPath;

function generateDiff(string $firstPath, string $secondPath, string $style = ''): string
{

    $pathToFile1 = getFixtureFullPath($firstPath);
    $pathToFile2 = getFixtureFullPath($secondPath);

    $fileContent1 = file_get_contents($pathToFile1);
    $fileContent2 = file_get_contents($pathToFile2);
    $jsonToArr1 = json_decode($fileContent1, true);
    $jsonToArr2 = json_decode($fileContent2, true);

    $merged = array_keys(array_merge($jsonToArr1, $jsonToArr2));

    $sortedKeys = sort($merged, fn ($left, $right) => strcmp($left, $right));

    $resultString = "{\n";

    foreach ($sortedKeys as $key) {
        if (!array_key_exists($key, $jsonToArr1)) {
            $resultString .= "\t" . '+ ' . $key . ': ' . (var_export($jsonToArr2[$key], true)) . "\n";
        } elseif (!array_key_exists($key, $jsonToArr2)) {
            $resultString .= "\t" . '- ' . $key . ': ' . (var_export($jsonToArr1[$key], true)) . "\n";
        } elseif ($jsonToArr1[$key] !== $jsonToArr2[$key]) {
            $resultString .= "\t" . '- ' . $key . ': ' . (var_export($jsonToArr1[$key], true)) . "\n";
            $resultString .= "\t" . '+ ' . $key . ': ' . (var_export($jsonToArr2[$key], true)) . "\n";
        } else {
            $resultString .= "\t" . '  ' . $key . ': ' . (var_export($jsonToArr1[$key], true)) . "\n";
        }
    }
    $resultString .= '}' . "\n";

    return $resultString;
}
