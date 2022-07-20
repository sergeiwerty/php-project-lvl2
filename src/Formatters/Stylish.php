<?php

namespace Differ\Formatters\stylish;

use function Functional\flatten;

const INDENT_SYMBOL = ' ';
const INITIAL_INDENT_SIZE = 2;
const EXTRA_INDENT_SIZE = 4;

/**
 * @param array<int, mixed> $astTreeData
 * @return string
 */
function makeFormattedDiff(array $astTreeData): string
{
    $addIndent = function ($depth, $initial) {
        return str_repeat(INDENT_SYMBOL, $initial + $depth * EXTRA_INDENT_SIZE);
    };

    $renderInnerValues = function ($value, $depth) use (&$renderInnerValues, $addIndent) {
        $valueCopy = $value;
        return array_reduce(
            array_keys($valueCopy),
            function ($acc, $key) use ($renderInnerValues, $addIndent, $valueCopy, $depth) {
                $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
                $indentKey = "{$indent}  {$key}";
                return is_array($valueCopy[$key])
                    ? [$acc, "{$indentKey}: {", $renderInnerValues($valueCopy[$key], $depth + 1), "  {$indent}}"]
                    : [$acc, "{$indentKey}: {$valueCopy[$key]}"];
            },
            []
        );
    };

    $stringify = function ($key, $value, $depth) use ($addIndent, $renderInnerValues) {
        $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
        if (is_array($value)) {
            return ["{$key}: {", $renderInnerValues($value, $depth + 1), "  {$indent}}"];
        }
        if ('' === $value) {
            return "{$key}: ";
        }
        if (is_null($value)) {
            return "{$key}: null";
        }
        if (is_bool($value)) {
            $correctValue = trim(var_export($value, true), "'");
            return "{$key}: {$correctValue}";
        }
        return "{$key}: {$value}";
    };

    $statusTree = [
        'added' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $plusKey = "{$indent}+ {$key}";
            return $stringify($plusKey, $value, $depth);
        },
        'deleted' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $minusKey = "{$indent}- {$key}";
            return $stringify($minusKey, $value, $depth);
        },
        'nested' => function ($node, $indent, $depth, $iterRender) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            $emptyKey = "{$indent}  {$key}";
            return ["{$emptyKey}: {", $iterRender($children, $depth + 1), "  {$indent}}"];
        },
        'changed' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) =>
                [
                    'key' => $key,
                    'node' => ['valueBeforeChange' => $valueBefore, 'valueAfterChange' => $valueAfter]
                ]
            ] = $node;
            $plusKey = "{$indent}+ {$key}";
            $minusKey = "{$indent}- {$key}";
            return [$stringify($minusKey, $valueBefore, $depth), $stringify($plusKey, $valueAfter, $depth)];
        },
        'unchanged' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $emptyKey = "{$indent}  {$key}";
            return $stringify($emptyKey, $value, $depth);
        }
    ];

    $renderStyledDiff = function ($data, $depth = 0) use (&$renderStyledDiff, $statusTree, $addIndent) {
        $lines = array_map(function ($node) use ($renderStyledDiff, $depth, $statusTree, $addIndent) {
            [key($node) => ['status' => $status]] = $node;
            $diffTypeHandler = $statusTree[$status];
            $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
            return $diffTypeHandler($node, $indent, $depth, $renderStyledDiff);
        }, $data);

        $styled = implode("\n", flatten($lines));
        return $depth === 0 ? "{\n{$styled}\n}" : $styled;
    };

    return $renderStyledDiff($astTreeData, 0);
}
