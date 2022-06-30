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
//            [$expectedStylish, '../tests/fixtures/nestedJson1.json', '../tests/fixtures/nestedJson2.json'],
//            [$expectedStylish, '../tests/fixtures/nestedYaml1.yaml', '../tests/fixtures/nestedYaml2.yaml'],
//            [$expectedStylish, '../tests/fixtures/nestedJson1.json', '../tests/fixtures/nestedJson2.json', 'stylish'],
//            [$expectedStylish, '../tests/fixtures/nestedYaml1.yaml', '../tests/fixtures/nestedYaml2.yaml', 'stylish'],
            [$expectedPlain, '../tests/fixtures/nestedJson1.json', '../tests/fixtures/nestedJson2.json', 'plain'],
            [$expectedPlain, '../tests/fixtures/nestedYaml1.yaml', '../tests/fixtures/nestedYaml2.yaml', 'plain'],
//            [$expectedJSON, '../tests/fixtures/nestedJson1.json', '../tests/fixtures/nestedJson2.json', 'json'],
//            [$expectedJSON, '../tests/fixtures/nestedYaml1.yaml', '../tests/fixtures/nestedYaml2.yaml', 'json'],
        ];
    }
}
