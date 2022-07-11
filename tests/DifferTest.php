<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getDataByFileName($filename): string
    {
        return trim(file_get_contents(__DIR__ . "/fixtures/" . $filename));
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithStylishFormatter($fileName1, $fileName2)
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
        $this->assertEquals($expectedStylish, genDiff($fileName1, $fileName2, 'stylish'));
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithPlainFormatter($fileName1, $fileName2)
    {
        $expectedPlain = $this->getDataByFileName("expectedPlain.txt");
        $this->assertEquals($expectedPlain, genDiff($fileName1, $fileName2, 'plain'));
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithJsonFormatter($fileName1, $fileName2)
    {
        $expectedJSON = $this->getDataByFileName("expectedJSON.txt");
        $this->assertEquals($expectedJSON, genDiff($fileName1, $fileName2, 'json'));
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithDefaultFormatter($fileName1, $fileName2)
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
        $this->assertEquals($expectedStylish, genDiff($fileName1, $fileName2));
    }

    public function nestedFilesProvider()
    {
        return [
            ['../tests/fixtures/nestedJson1.json', '../tests/fixtures/nestedJson2.json'],
            ['../tests/fixtures/nestedYaml1.yaml', '../tests/fixtures/nestedYaml2.yaml'],
        ];
    }
}
