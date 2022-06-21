<?php

namespace Differ\Differ\Builder;

use function Functional\sort;
use function Differ\Parsers\Parser\parse;
use function Differ\Formatters\JSONFormatter\buildStyledFormat;

function buildDiff($firstPath, $secondPath,)
{
    $parsedNodes1 = parse($firstPath);
    $parsedNodes2 = parse($secondPath);

    $dataByStrings1 = trim(var_export($parsedNodes1, true), "'");
//    print_r($dataByStrings1);

    $dataByStrings2 = trim(var_export($parsedNodes2, true), "'");

//    $iter = function($currentValue) use (&$iter) {
//        if(!is_array($currentValue)) {
//            print_r($currentValue);
//            return $currentValue;
//        }
//
//        $children = array_map(
//            function ($key, $value) use (&$iter) {
//                var_dump($key, $value);
//                return [$key => $value, 'children' => $iter($value)];
//            },
//            array_keys($currentValue),
//            $currentValue
//        );
//        print_r($children);
//        return $iter;
//    };

//    $my = ['nodeList', [['leafNode', 'myKey', 'myValue', 'unchanged'], ['leafNode', 'otherKey', 'otherValue', 'changed']]];

    $my = function($currentNode1, $currentNode2) use (&$my) {
        $merged = array_keys(array_merge($currentNode1, $currentNode2));
        $sortedKeys = sort($merged, fn($left, $right) => strcmp($left, $right));

        print_r($currentNode1);
        print_r($currentNode2);

        return array_map(
            function($key) use (&$my, $currentNode1, $currentNode2) {
                $result = [];
//                var_dump($currentNode1);
                if (!array_key_exists($key, $currentNode1)) {
                    $nodeType = is_array($currentNode2[$key]) ? 'listNode' : 'leafNode';
//                    $nodeType === 'leafNode'? 'leafNode' : $iter($parsedNodes2[$key], $)
                    $result[] = ['type' => $nodeType, 'key' => $key, 'value' => $currentNode2[$key], 'added'];
//                    print_r($result);
                    return $result;
                } elseif (!array_key_exists($key, $currentNode2)) {
                    $nodeType = is_array($currentNode1[$key]) ? 'listNode' : 'leafNode';
                    $result[] = ['type' => $nodeType, 'key' => $key, 'value' => $currentNode1[$key], 'deleted'];
                    return $result;
                } elseif ($currentNode1[$key] !== $currentNode2[$key]) {
                    $nodeType = is_array($currentNode2[$key]) ? 'listNode' : 'leafNode';
                    // => $iter($parsedNodes1[$key], $parsedNodes2[$key]);
//                    'value' => $parsedNodes2[$key]
                    $state = $nodeType === 'leafNode' ? [$currentNode1[$key], $currentNode2[$key]] : $my($currentNode1[$key], $currentNode2[$key]);
                    $result[] = ['type' => $nodeType, 'key' => $key, 'value' => $state, 'changed'];
//                    print_r($result);
                    return $result;
                } else {
                    $nodeType = is_array($currentNode1[$key]) ? 'listNode' : 'leafNode';
                    $result[] = ['type' => $nodeType, 'key' => $key, 'value' => $currentNode1[$key],'unchanged'];
                    return $result;
                }

            },
            $sortedKeys,
            $currentNode1,
            $currentNode2
        );
    };

//    var_dump(json_encode($my($parsedNodes1, $parsedNodes2)));
//    return $my($parsedNodes1, $parsedNodes2);
    return buildStyledFormat($my($parsedNodes1, $parsedNodes2));
}
