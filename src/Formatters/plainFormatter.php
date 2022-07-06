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
}

/**
 * @param array<int, array> $astTreeData
 * @return string
 */
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
     * @param array<int, array> $diff
     * @return string
     */
    $renderPlainDiff = function (array $diff, string|bool $pathComposition) use (&$renderPlainDiff, $statusTree) {
        $diffCopy = $diff;
        $lines = array_reduce(
            $diffCopy,
            /**
             * @param array<int, string> $acc
             * @param array<string, array> $node
             * @param array|null $initial
             * @return array
             */
            function (array $acc, array $node, array|null $initial = []) use ($renderPlainDiff, $pathComposition, $statusTree) {
                [key($node) => ['status' => $status, 'key' => $key]] = $node;
                $newPath = $pathComposition ? "{$pathComposition}.{$key}" : "{$key}";
                $diffTypeHandler = $statusTree[$status];
                return flatten([$acc, $diffTypeHandler($newPath, $node, $renderPlainDiff)]);
            },
            []
        );

        return implode("\n", $lines);
    };

    return $renderPlainDiff($astTreeData, false);
}
