<?php

namespace Differ\Differ;

use function Functional\sort;

function generateDiff(string $firstPath, string $secondPath, string $style = ''): string
{
    chdir('tests/fixtures');

    $absolutePath1 = realpath($firstPath);
    $absolutePath2 = realpath($secondPath);

    $fileContent1 = file_get_contents($absolutePath1);
    $fileContent2 = file_get_contents($absolutePath2);

    $jsonToArr1 = json_decode($fileContent1, true);
    $jsonToArr2 = json_decode($fileContent2, true);
    /////

//    $absolutePath1 = __DIR__ . '/' . pathinfo($firstPath, PATHINFO_DIRNAME) . '/' . pathinfo($firstPath, PATHINFO_BASENAME);
//    print_r($absolutePath1);
//    print_r("\n");
//    print_r(__DIR__);
//    print_r("\n");
//    print_r(realpath(""));
//    print_r("\n");
//    print_r(getcwd() . "\n");
////    chdir(realpath(""));
//    var_dump(realpath($firstPath));
//    $myValue = realpath($firstPath);
//    print_r("\n");
//    print_r(error_get_last());
//    $absolutePath2 = __DIR__ . '/' . pathinfo($secondPath, PATHINFO_DIRNAME) . '/' . pathinfo($secondPath, PATHINFO_BASENAME);
//    $myValue2 = realpath($secondPath);

    $fileContent1 = file_get_contents($absolutePath1);

    $fileContent2 = file_get_contents($absolutePath2);
//    print_r($secondPath);

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

//print_r(generateDiff('../tests/fixtures/fixture1.json', '../tests/fixtures/fixture2.json'));
