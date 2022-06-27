<?php

namespace Differ\Formatters\JSONFormatter;

use function Functional\flatten;

const INDENT_SYMBOL = ' ';
const INITIAL_INDENT_SIZE = 2;
const EXTRA_INDENT_SIZE = 4;

function buildStyledFormat($data)
{
    $addIndent = function ($depth, $initial) {
        return str_repeat(INDENT_SYMBOL, $initial + $depth * EXTRA_INDENT_SIZE);
    };

    $renderInnerValues = function ($value, $depth) use (&$renderInnerValues, $addIndent) {
        return array_reduce(array_keys($value), function ($acc, $key) use ($renderInnerValues, $addIndent, $value, $depth) {
            $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
            $indentKey = "{$indent}  {$key}";
            return is_array($value[$key])
                ? [$acc, "{$indentKey}: {", $renderInnerValues($value[$key], $depth + 1), "  {$indent}}"]
                : [$acc, "{$indentKey}: {$value[$key]}"];
        }, []);
    };

    $stringify = function ($key, $value, $depth) use ($addIndent, $renderInnerValues) {
        $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
        if (is_array($value)) {
            return ["{$key}: {", $renderInnerValues($value, $depth + 1), "  {$indent}}"];
        }
        if ('' === $value) {
            return "{$key}:";
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
            print_r($node);
            return $stringify($plusKey, $value, $depth);
        },
        'deleted' => function ($node, $indent, $depth) use ($stringify) {
            print_r('');
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $minusKey = "{$indent}- {$key}";
            return $stringify($minusKey, $value, $depth);
        },
        'nested' => function ($node, $indent, $depth, $iterRender) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'children' => $children]]] = $node;
            $emptyKey = "{$indent}  {$key}";
            print_r($node);
            return ["{$emptyKey}: {", $iterRender($children, $depth + 1), "  {$indent}}"];
        },
        'changed' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value, 'valueBeforeChange' => $valueBefore, 'valueAfterChange' => $valueAfter]]] = $node;
            $plusKey = "{$indent}+ {$key}";
            $minusKey = "{$indent}- {$key}";
            print_r($node);
            return [$stringify($minusKey, $valueBefore, $depth), $stringify($plusKey, $valueAfter, $depth)];
        },
        'unchanged' => function ($node, $indent, $depth) use ($stringify) {
            [key($node) => ['key' => $key, 'node' => ['value' => $value]]] = $node;
            $emptyKey = "{$indent}  {$key}";
            print_r($node);
            return $stringify($emptyKey, $value, $depth);
        }
    ];

    $renderDiff = function ($data, $depth = 0) use (&$renderDiff, $statusTree, $addIndent)
    {
        $lines = array_map(function ($node) use ($renderDiff, $depth, $statusTree, $addIndent) {
            [key($node) => ['status' => $status]] = $node;
            $diffTypeHandler = $statusTree[$status];
            $indent = $addIndent($depth, INITIAL_INDENT_SIZE);
            return $diffTypeHandler($node, $indent, $depth, $renderDiff);
        }, $data);

        $styled = implode("\n", flatten($lines));
        return $depth === 0 ? "{\n{$styled}\n}" : $styled;
    };

    return $renderDiff($data, 0);
}
