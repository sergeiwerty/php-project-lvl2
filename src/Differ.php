<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parser\parse;
use function Differ\Formatter\getFormattedDiff;

/**
 * @param string $fileName
 * @return array<int, string|false>
 */
function getFileContent(string $fileName): array
{

    if (strpos($fileName, '/') === 0) {
        $parts = [$fileName];
    } else {
        $parts = [__DIR__, '/../', $fileName];
    }
    $absolutePath = implode('', $parts);

    $fileContent = file_get_contents($absolutePath);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    return [$fileContent, $fileType];
}

/**
 * @param array<int, mixed> $firstFileContent
 * @param array<int, mixed> $secondFileContent
 * @return array<int, mixed>
 */
function buildDiff(array $firstFileContent, array $secondFileContent,): array
{
    $iterAst = function ($currentNode1, $currentNode2) use (&$iterAst) {
        $merged = array_keys(array_merge($currentNode1, $currentNode2));
        $sortedKeys = sort($merged, fn($left, $right) => strcmp($left, $right));

        return array_map(
            function ($key) use (&$iterAst, $currentNode1, $currentNode2) {
                $result = [];
                if (!array_key_exists($key, $currentNode1)) {
                    return [...$result, $key =>
                        ['key' => $key, 'status' => 'added',
                            'node' => ['value' => $currentNode2[$key]]
                        ]
                    ];
                }
                if (!array_key_exists($key, $currentNode2)) {
                    return [...$result, $key =>
                        ['key' => $key, 'status' => 'deleted', 'node' =>
                            ['value' => $currentNode1[$key]]
                        ]
                    ];
                }
                if (is_array($currentNode1[$key]) && is_array($currentNode2[$key])) {
                    return [...$result, $key =>
                        ['key' => $key, 'status' => 'nested', 'node' =>
                            ['value' => $key, 'children' => $iterAst($currentNode1[$key], $currentNode2[$key])]
                        ]
                    ];
                }
                if ($currentNode1[$key] !== $currentNode2[$key]) {
                    return [...$result,
                        $key => ['key' => $key,
                            'status' => 'changed',
                            'node' => [
                                'value' => $currentNode2[$key],
                                'valueBeforeChange' => $currentNode1[$key],
                                'valueAfterChange' => $currentNode2[$key]
                            ]
                        ]

                    ];
                }
                return [...$result, $key =>
                    ['key' => $key, 'status' => 'unchanged', 'node' =>
                        ['value' => $currentNode1[$key]]
                    ]
                ];
            },
            $sortedKeys,
            $currentNode1,
            $currentNode2
        );
    };

    return $iterAst($firstFileContent, $secondFileContent);
}

function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string|false
{
    $firstData = parse(getFileContent($firstPath));
    $secondData = parse(getFileContent($secondPath));

    $diffOfFiles = buildDiff($firstData, $secondData);

    return getFormattedDiff($diffOfFiles, $format);
}
