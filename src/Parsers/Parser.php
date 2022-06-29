<?php

namespace Differ\Parsers\Parser;

use Symfony\Component\Yaml\Yaml;

function getFileContent($fileName): array
{
//    $parts = [__DIR__, '../../tests/fixtures', $fileName];
//    $absolutePath = realpath(implode('/', $parts));


    if (strpos($fileName, '/') === 0) {
        $parts = [$fileName];
    } else {
        $parts = [__DIR__, '/../', $fileName];
    }
    $absolutePath = implode('', $parts);


//    $pathToFile = getFullPath($fileName);
    $fileContent = file_get_contents($absolutePath);
//    $fileContent = file_get_contents($fileName);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    return [$fileContent, $fileType];
}

//function getFileContent(string $fileName): array
//{
////    print_r((__DIR__ . $fileName));
//    if (strpos($fileName, '/') === 0) {
//        print_r('');
//    }
//    print_r('');
//    $fileContent = file_get_contents(__DIR__ . '/../' . $fileName);
//    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
//    return [$fileContent, $fileType];
//}

function parse($fileName)
{
    $rawData = getFileContent($fileName);
    [$content, $type] = $rawData;

    $mapping = [
        'yml' =>
            fn($rawData) => Yaml::parse($rawData),
        'yaml' =>
            fn($rawData) => Yaml::parse($rawData),
        'json' =>
            fn($rawData) => json_decode($rawData, true)
    ];

    return $mapping[$type]($content);
}
