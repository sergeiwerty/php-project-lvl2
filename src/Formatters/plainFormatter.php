<?php

namespace Differ\Formatters\plainFormatter;

use function Functional\flatten;
use function Functional\reduce_left;

function prepareValue(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        return "[complex value]";
    }
    if (is_string($value)) {
        return "'{$value}'";
    }
    return "{$value}";
};

function makeFormattedDiff(array $astTreeData): string
{
    $statusTree = [
        'added' => function ($path, $node) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $value = prepareValue($value);
            return "Property '{$path}' was added with value: {$value}";
        },
        'deleted' => fn($path) => "Property '{$path}' was removed",
        'nested' => function ($path, $node, $iterRender) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            return $iterRender($children, $path);
        },
        'changed' => function ($path, $node) {
            [key($node) =>
                ['node' =>
                    ['valueBeforeChange' => $nodeValueBefore, 'valueAfterChange' => $nodeValueAfter]
                ]
            ] = $node;
            $valueBefore = prepareValue($nodeValueBefore);
            $valueAfter = prepareValue($nodeValueAfter);
            return "Property '{$path}' was updated. From {$valueBefore} to {$valueAfter}";
        },
        'unchanged' => fn() => [],
    ];

    /**
     * @param array $diff
     * @param bool|string $pathComposition
     * @return string
     */
    $renderPlainDiff = function (array $diff, bool|string $pathComposition) use (&$renderPlainDiff, $statusTree) {
        $lines = reduce_left($diff, function ($node, int $index, array $array, array $acc = []) use ($renderPlainDiff, $pathComposition, $statusTree) {
            [key($node) => ['status' => $status, 'key' => $key, 'node' => ['value' => $value]]] = $node;
            $newPath = $pathComposition ? "{$pathComposition}.{$key}" : "{$key}";
            $diffTypeHandler = $statusTree[$status];
//            $my = $diffTypeHandler($newPath, $node, $renderPlainDiff);
            $val = $acc ?? [];
            return flatten([...$val, $diffTypeHandler($newPath, $node, $renderPlainDiff)]);
        });

        return implode("\n", $lines);
    };

    return $renderPlainDiff($astTreeData, false);
}

