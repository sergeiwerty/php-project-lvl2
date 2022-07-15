<?php

namespace Differ\Builder;

use function Functional\sort;

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
