<?php

namespace Differ\Differ\Builder;

use function Functional\sort;
use function Differ\Parsers\Parser\parse;
use function Differ\Formatters\plainFormatter\makeFormattedDiff;
//use function Differ\Formatters\stylishFormatter\buildStyledFormat;


function buildDiff($firstPath, $secondPath,)
{
    $parsedNodes1 = parse($firstPath);
    $parsedNodes2 = parse($secondPath);

    $dataByStrings1 = trim(var_export($parsedNodes1, true), "'");
//    print_r($dataByStrings1);

    $dataByStrings2 = trim(var_export($parsedNodes2, true), "'");

    $iterAst = function($currentNode1, $currentNode2) use (&$iterAst) {
//        print_r($currentNode1);
//        print_r($currentNode2);
        $merged = array_keys(array_merge($currentNode1, $currentNode2));
        $sortedKeys = sort($merged, fn($left, $right) => strcmp($left, $right));

        return array_map(
            function($key) use (&$iterAst, $currentNode1, $currentNode2, $sortedKeys) {
                $result = [];
                if (!array_key_exists($key, $currentNode1)) {
//                    print_r('');
                    return [...$result, $key => ['key' => $key, 'status' => 'added', 'node' => ['value' => $currentNode2[$key]]]];
                }
                if (!array_key_exists($key, $currentNode2)) {
//                    print_r('');
                    return [...$result, $key => ['key' => $key, 'status' => 'deleted', 'node' => ['value' => $currentNode1[$key]]]];
                }
                if (is_array($currentNode1[$key]) && is_array($currentNode2[$key])) {
//                    print_r('');
                    return [...$result, $key => ['key' => $key, 'status' => 'nested', 'node' => ['value' => $key, 'children' => $iterAst($currentNode1[$key], $currentNode2[$key])]]];
                }
                if ($currentNode1[$key] !== $currentNode2[$key]) {
//                    print_r('');
//                    $state = $nodeType === 'leafNode' ? [$currentNode1[$key], $currentNode2[$key]] : $iterAst($currentNode1[$key], $currentNode2[$key]);
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
//                print_r($key);
                return [...$result, $key => ['key' => $key, 'status' =>'unchanged', 'node' => ['value' => $currentNode1[$key]]]];


            },
            $sortedKeys,
            $currentNode1,
            $currentNode2
        );
    };

//    var_dump(json_encode($my($parsedNodes1, $parsedNodes2)));
//    var_dump(buildStyledFormat($iterAst($parsedNodes1, $parsedNodes2)));
    return makeFormattedDiff($iterAst($parsedNodes1, $parsedNodes2));

}
