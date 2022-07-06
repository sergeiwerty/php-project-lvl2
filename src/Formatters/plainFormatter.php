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
 * @param array<int, mixed> $astTreeData
 * @return string
 */
function makeFormattedDiff(array $astTreeData): string
{
    $statusTree = [
        'added' => function (string $path, array $node) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $preparedValue = prepareValue($value);
            return "Property '{$path}' was added with value: {$preparedValue}";
        },
        'deleted' => fn($path) => "Property '{$path}' was removed",
        'nested' => function (string $path, array $node, callable $iterRender) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            return $iterRender($children, $path);
        },
        'changed' => function (string $path, array $node) {
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
     * @param string|bool $pathComposition
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
            function (
                array $acc,
                array $node,
                array|null $initial = []
            ) use (
                $renderPlainDiff,
                $pathComposition,
                $statusTree
            ) {
                [key($node) => ['status' => $status, 'key' => $key]] = $node;
                $newPath = is_string($pathComposition) ? "{$pathComposition}.{$key}" : "{$key}";
                $diffTypeHandler = $statusTree[$status];
                return flatten([$acc, $diffTypeHandler($newPath, $node, $renderPlainDiff)]);
            },
            []
        );

        return implode("\n", $lines);
    };

    return $renderPlainDiff($astTreeData, false);
}
