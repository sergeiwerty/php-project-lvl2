<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{

    /**
     * @dataProvider equalityNestedProvider
     */
    public function testNestedEquals($expected, $fileName1, $fileName2, $format = 'stylish')
    {
        $this->assertEquals($expected, genDiff($fileName1, $fileName2, $format));
    }

    public function equalityNestedProvider()
    {
        $expectedStylish = trim(file_get_contents(__DIR__ . "/fixtures/" . "expectedStylish.txt"));
        $expectedPlain = trim(file_get_contents(__DIR__ . "/fixtures/" . "expectedPlain.txt"));
        $expectedJSON = trim(file_get_contents(__DIR__ . "/fixtures/" . "expectedJSON.txt"));

        return [
            [$expectedStylish, 'nestedJson1.json', 'nestedJson2.json'],
            [$expectedStylish, 'nestedYaml1.yaml', 'nestedYaml2.yaml'],
            [$expectedStylish, 'nestedJson1.json', 'nestedJson2.json', 'stylish'],
            [$expectedStylish, 'nestedYaml1.yaml', 'nestedYaml2.yaml', 'stylish'],
            [$expectedPlain, 'nestedJson1.json', 'nestedJson2.json', 'plain'],
            [$expectedPlain, 'nestedYaml1.yaml', 'nestedYaml2.yaml', 'plain'],
            [$expectedJSON, 'nestedJson1.json', 'nestedJson2.json', 'json'],
            [$expectedJSON, 'nestedYaml1.yaml', 'nestedYaml2.yaml', 'json'],
        ];
    }

}
