<?php

namespace Differ\Formatters\plainFormatter;

use function Functional\flatten;

function makeFormattedDiff($astTreeData): string
{

    $prepareValue = function ($value) {
        if (is_null($value)) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            print_r($value);
            return "[complex value]";
        }
        if (is_string($value)) {
            return "'{$value}'";
        }
        return "{$value}";
    };

    $statusTree = [
        'added' => function ($path, $node) use ($prepareValue) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            print_r($value);

            $value = $prepareValue($value);
            print_r($value);
            return "Property '{$path}' was added with value: {$value}";
        },
        'deleted' => fn($path) => "Property '{$path}' was removed",
        'nested' => function ($path, $node, $iterRender) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            return $iterRender($children, $path);
        },
        'changed' => function ($path, $node) use ($prepareValue) {
            [key($node) =>
                ['node' =>
                    ['valueBeforeChange' => $nodeValueBefore, 'valueAfterChange' => $nodeValueAfter]
                ]
            ] = $node;
            $valueBefore = $prepareValue($nodeValueBefore);
            $valueAfter = $prepareValue($nodeValueAfter);
            return "Property '{$path}' was updated. From {$valueBefore} to {$valueAfter}";
        },
        'unchanged' => fn() => [],
    ];

    $renderPlainDiff = function ($diff, $pathComposition) use (&$renderPlainDiff, $statusTree) {
        $lines = array_reduce($diff, function ($acc, $node) use ($renderPlainDiff, $pathComposition, $statusTree) {
            [key($node) => ['status' => $status, 'key' => $key, 'node' => ['value' => $value]]] = $node;
            $newPath = $pathComposition ? "{$pathComposition}.{$key}" : "{$key}";
            $diffTypeHandler = $statusTree[$status];
            return flatten([...$acc, $diffTypeHandler($newPath, $node, $renderPlainDiff)]);
        }, []);

        return implode("\n", $lines);
    };

    return $renderPlainDiff($astTreeData, false);
}
