<?php
//$autoloadPath1 = __DIR__ . '/../../../autoload.php';
//$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
//
//if (file_exists($autoloadPath1)) {
//    require_once $autoloadPath1;
//} else {
//    require_once $autoloadPath2;
//}

namespace Differ\Differ;

use function Functional\sort;

function generateDiff($firstPath, $secondPath, $style = '') : string
{
    $fileContent1 = file_get_contents(realpath($firstPath));
    print_r($fileContent1);
    $fileContent2 = file_get_contents(realpath(realpath($secondPath)));
    print_r($fileContent2);

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
    $resultString .= '}';

    return $resultString;
}

//print_r(genDiff());