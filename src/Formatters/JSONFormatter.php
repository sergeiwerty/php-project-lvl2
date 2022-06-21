<?php

//use function Differ\Formatters\JSONFormatter;
namespace Differ\Formatters\JSONFormatter;

function buildStyledFormat($data)
{
    $iter = function ($currentData, $depth) use (&$iter, ) {
        if (!is_array($currentData)) {
            $my = trim(var_export($currentData, true), "'");
            print_r($my);
            return trim(var_export($currentData, true), "'");
        }

        $indentSize = $depth;
        $currentIndent = str_repeat(' ', $indentSize);
        $bracketIndent = str_repeat(' ', $indentSize - 1);

        $lines = array_map(
//            fn($key, $value) => "{$currentIndent}{$key}: {$iter($value, $depth + 1)}",
        function ($key, $value) use ($currentData, $iter, $currentIndent, $depth) {
            print_r(array_keys($currentData));
            print_r($currentData[0][0]['key']);
//            return "{$currentIndent}{$key}: {$iter($value, $depth + 1)}";
            return "{$currentIndent}{$currentData[0][0]['key']}: {$iter($value, $depth + 1)}";

        },
            array_keys($currentData),
            $currentData
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($data, 1);
}


//return trim(var_export($value, true), "'");
