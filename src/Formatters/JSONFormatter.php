<?php

//use function Differ\Formatters\JSONFormatter;
namespace Differ\Formatters\JSONFormatter;

use function Functional\flatten;

function buildStyledFormat($data)
{
    print_r($data);
    $flatten = array_merge(...$data);
    print_r($flatten);

    $iter = function ($currentData, $depth) use (&$iter, ) {
        print_r($currentData);


        if (!is_array($currentData)) {
            $my = trim(var_export($currentData, true), "'");
            print_r($my);
            return trim(var_export($currentData, true), "'");
        }

//        print_r($currentData);
//        $otherFlatten = array_merge($currentData);
//        print_r($otherFlatten);

        $indentSize = $depth;
        $currentIndent = str_repeat(' ', $indentSize);
        $bracketIndent = str_repeat(' ', $indentSize - 1);

        $lines = array_map(function ($key, $value) use ($currentData, $iter, $currentIndent, $depth) {
            print_r($value);
            if (is_string($value)) {
                return "{$currentIndent}{$key}: {$value}";
            }
//                $currKey = key($value);
            if (!is_array($value['value'])) {
                print_r('');
                return "{$currentIndent}{$key}: {$value['value']}";
            }
            if (count($value['value']) === 2 && $value['status'] === 'changed') {
                print_r(count($value));
                return "{$currentIndent}{$key}: {$iter($value['value'][0], $depth + 1)}\n {$currentIndent}{$key}: {$iter($value['value'][1], $depth + 1)}";
            } elseif (count($value['value']) === 1) {
                print_r('');
                $vl = flatten($value['value'])[0];
                return "{$currentIndent}{$key}: {$vl}";
            } else {
                print_r('');
                $fl = array_merge(...$value['value']);
            }

//            elseif () {fl = array_merge($value['value']);}

//            print_r($value['value']);
//            $fl = '';
//            if (count($value['value']) === 1) {
//                $k = key($value['value']);
//                $v = current($value['value']);
//                print_r('');
//
////                return "{$currentIndent}{$k}: {$v}";
//
//                return "{$currentIndent}{$k}: {$v}";


            print_r('');
            return "{$currentIndent}{$key}: {$iter($fl, $depth + 1)}";
        }, array_keys($currentData), $currentData);

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($flatten, 1);
}


//return trim(var_export($value, true), "'");
