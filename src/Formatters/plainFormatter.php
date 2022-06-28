<?php

namespace Differ\Formatters\plainFormatter;

use function Functional\flatten;

function makeFormattedDiff ($astTreeData): string
{
    $renderValue = function ($value) {
        $renderedValue = is_array($value) ? "[complex value]" : $value;
        return $renderedValue;
    };

    $statusTree = [
        'added' => function ($path, $node) use ($renderValue) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            print_r($value);

            $value = $renderValue($value);
            return "Property {$path} wad added with value: {$value}";
        },
        'deleted' => fn($path) => "Property {$path} was removed",
//        'nested' => fn($path, $node, $iterRender) => $iterRender($node['children'], $path),
        'nested' => function ($path, $node, $iterRender) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            print_r($children);
//            return $iterRender($node['children'], $path);
            return $iterRender($children, $path);

        },
        'changed' => function ($path, $node) use ($renderValue) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'valueBeforeChange' => $nodeValueBefore, 'valueAfterChange' => $nodeValueAfter]]] = $node;
            $valueBefore = $renderValue($nodeValueBefore);
            $valueAfter = $renderValue($nodeValueAfter);
            return "Property {$path} was changed from {$valueBefore} to {$valueAfter}";
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


